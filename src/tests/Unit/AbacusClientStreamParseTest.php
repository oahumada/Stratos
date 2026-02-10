<?php

use App\Services\AbacusClient;

class FakeStream
{
    protected array $pieces;
    protected int $index = 0;

    public function __construct(array $pieces)
    {
        $this->pieces = $pieces;
    }

    public function read($len)
    {
        if ($this->index >= count($this->pieces)) return '';
        return $this->pieces[$this->index++];
    }

    public function eof()
    {
        return $this->index >= count($this->pieces);
    }
}

class FakeResponse
{
    protected FakeStream $stream;

    public function __construct(FakeStream $stream)
    {
        $this->stream = $stream;
    }

    public function getBody()
    {
        return $this->stream;
    }
}

// We'll create an anonymous Guzzle client in tests to satisfy the typed property

it('assembles content when data lines are split across reads', function () {
    // build a stream where a single data JSON line is split across two reads
    $part1 = 'data: {"choices":[{"delta":{"content":"he';
    $part2 = 'llo"}}]}' . "\n\n" . 'data: [DONE]'."\n";

    $stream = new FakeStream([$part1, $part2]);
    $client = new class extends AbacusClient {
        public function __construct() {}
        public function testParse($body, $options = [], $onChunk = null) { return $this->parseStream($body, $options, $onChunk); }
    };

    $deltas = [];
    $metas = [];
    $res = $client->testParse($stream, [], function ($delta, $meta = null) use (&$deltas, &$metas) {
        $deltas[] = $delta;
        $metas[] = $meta;
    });

    expect(implode('', $deltas))->toBe('hello');
    expect($res['content'])->toBe('hello');
});

it('ignores malformed json lines and continues parsing valid ones', function () {
    $good = 'data: {"choices":[{"delta":{"content":"ok"}}]}' . "\n\n";
    $bad = 'data: {this is not json}' . "\n\n";
    $done = 'data: [DONE]' . "\n";

    $stream = new FakeStream([$bad, $good, $done]);
    $client = new class extends AbacusClient {
        public function __construct() {}
        public function testParse($body, $options = [], $onChunk = null) { return $this->parseStream($body, $options, $onChunk); }
    };

    $deltas = [];
    $res = $client->testParse($stream, [], function ($delta, $meta = null) use (&$deltas) {
        $deltas[] = $delta;
    });

    expect(implode('', $deltas))->toBe('ok');
    expect($res['content'])->toBe('ok');
});
