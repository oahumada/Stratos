<?php

use App\Services\AbacusClient;

it('retries on transient failures and eventually succeeds', function () {
    $responseBody = json_encode(['response' => ['ok' => true]]);

    $http = new class extends \GuzzleHttp\Client {
        protected $body = null;
        protected $calls = 0;
        public function __construct() {}
        public function post($uri, array $options = []): \Psr\Http\Message\ResponseInterface {
            $this->calls++;
            if ($this->calls <= 2) {
                throw new class('sim') extends \RuntimeException implements \GuzzleHttp\Exception\GuzzleException {};
            }
            return new \GuzzleHttp\Psr7\Response(200, [], json_encode(['response' => ['ok' => true]]));
        }
    };

    $client = new \App\Services\AbacusClient($http);

    $res = $client->generate('prompt', ['retries' => 3]);
    expect(is_array($res))->toBeTrue();
    expect($res['response']['ok'])->toBeTrue();
});

it('throws after exceeding max retries', function () {
    $http = new class extends \GuzzleHttp\Client {
        protected $calls = 0;
        public function __construct() {}
        public function post($uri, array $options = []): \Psr\Http\Message\ResponseInterface {
            $this->calls++;
            throw new class('fail') extends \RuntimeException implements \GuzzleHttp\Exception\GuzzleException {};
        }
    };

    $client = new \App\Services\AbacusClient($http);

    try {
        $client->generate('p', ['retries' => 2]);
        throw new \Exception('Expected exception not thrown');
    } catch (\Throwable $e) {
        expect($e)->toBeInstanceOf(\GuzzleHttp\Exception\GuzzleException::class);
    }
});
