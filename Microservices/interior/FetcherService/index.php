<?php
namespace PHPKare\Microservices\Internal;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class FetcherService {
    private $client;

    public function __construct() {
        $this->client = new Client(['verify' => false]);
    }

    public function fetch($url, $headers = []) {
        try {
            $response = $this->client->get($url, ['headers' => $headers]);
            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            error_log("Error fetching data: " . $e->getMessage());
            return ['error' => 'Unable to fetch data'];
        }
    }
}

