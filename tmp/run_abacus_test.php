<?php
require __DIR__ . '/../src/vendor/autoload.php';

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

$part1 = 'data: {"choices":[{"delta":{"content":"he"}';
$part2 = 'llo"}}]}' . "\n\n" . 'data: [DONE]'."\n";
$stream = new FakeStream([$part1, $part2]);
$resp = new FakeResponse($stream);
$http = new class($resp) extends \GuzzleHttp\Client {
    protected $resp;
    public function __construct($resp) { $this->resp = $resp; }
    public function post($url, $opts = []) { return $this->resp; }
}($resp);

$client = new class($http) extends AbacusClient {
    public function __construct($http)
    {
        $this->http = $http;
    }
};

$deltas = [];
$res = $client->generateStream('p', [], function ($delta, $meta = null) use (&$deltas) {
    $deltas[] = $delta;
});

var_dump(implode('', $deltas));
var_dump($res);
