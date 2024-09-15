<?php

namespace PHPKare\Microservices;

class FetcherService
{
    public function fetchVulnerabilities($manufacturer, $os, $version, $type, $nodeId)
    {
        $url = "fetch_vulnerabilities.php?manufacturer=$manufacturer&product=$os&version=$version&type=$type";
        $response = file_get_contents($url);
        return json_decode($response, true);
    }
}
