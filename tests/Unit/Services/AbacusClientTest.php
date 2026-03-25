<?php

use App\Services\AbacusClient;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;

uses(Tests\TestCase::class);

test('it can generate a response', function () {
    $container = [];
    $history = Middleware::history($container);

    $mock = new MockHandler([
        new Response(200, [], json_encode(['choices' => [['message' => ['content' => 'Hello response']]]])),
    ]);

    $handlerStack = HandlerStack::create($mock);
    $handlerStack->push($history);
    $client = new Client(['handler' => $handlerStack]);

    $abacus = new AbacusClient($client);
    $response = $abacus->generate('Hello world');

    expect($response)->toBeArray()
        ->and($response['choices'][0]['message']['content'])->toBe('Hello response');

    expect($container)->toHaveCount(1);
    $request = $container[0]['request'];
    expect($request->getUri()->getPath())->toBe('/v1/generate');
});

test('it retries on 500 errors', function () {
    $mock = new MockHandler([
        new Response(500),
        new Response(500),
        new Response(200, [], json_encode(['success' => true])),
    ]);

    $handlerStack = HandlerStack::create($mock);
    $client = new Client(['handler' => $handlerStack]);

    $abacus = new AbacusClient($client);
    $response = $abacus->generate('Retry test', ['retries' => 2]);

    expect($response['success'])->toBeTrue();
});

test('it throws exception after max retries', function () {
    $mock = new MockHandler([
        new Response(500),
        new Response(500),
        new Response(500),
    ]);

    $handlerStack = HandlerStack::create($mock);
    $client = new Client(['handler' => $handlerStack]);

    $abacus = new AbacusClient($client);

    expect(fn () => $abacus->generate('Fail test', ['retries' => 2]))
        ->toThrow(\GuzzleHttp\Exception\ServerException::class);
});
