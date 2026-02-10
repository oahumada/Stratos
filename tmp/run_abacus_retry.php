<?php
require __DIR__ . '/../src/vendor/autoload.php';

use App\Services\AbacusClient;

$fakeResp = new class('{"response": {"ok": true}}') {
    protected $body;
    public function __construct($body) { $this->body = $body; }
    public function getBody() { return $this->body; }
};

$http = new class($fakeResp) extends \GuzzleHttp\Client {
    protected $resp;
    protected $calls = 0;
    public function __construct($resp) { $this->resp = $resp; }
    public function post($uri, array $options = []) {
        $this->calls++;
        echo "post called: {$this->calls}\n";
        if ($this->calls <= 2) {
            throw new class('sim') extends \RuntimeException implements \GuzzleHttp\Exception\GuzzleException {};
        }
        return $this->resp;
    }
};

$client = new class($http) extends AbacusClient {
    public function __construct($http) { $this->http = $http; }
};

echo "calling generate...\n";
$res = $client->generate('p', ['retries' => 3]);
var_dump($res);
