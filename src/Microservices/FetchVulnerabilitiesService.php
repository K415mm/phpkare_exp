<?php
namespace PHPKare\Microservices\external_in_out;

use PHPKare\Microservices\interior\FetcherService;
use PHPKare\Microservices\external\APICoordinator;

class FetchVulnerabilitiesService {
    private $fetcherService;
    private $apiCoordinator;
    private $apiKey = 'cb4c4a74-bad6-45ca-9871-e2d09b11d200';

    public function __construct() {
        $this->fetcherService = new FetcherService();
        $this->apiCoordinator = new APICoordinator();
        $this->apiCoordinator->addObserver($this);
    }

    public function fetch($manufacturer, $product, $version, $type) {
        $vulnerabilities = [];
        $ntype = [
            "h" => ["router", "switch", "firewall"],
            "a" => ["database"],
            "o" => ["router", "workstation", "firewall"],
        ];

        foreach ($ntype as $cpeType => $products) {
            if (in_array($type, $products)) {
                $result = $this->fetchVulnerabilities($manufacturer, $product, $version, $cpeType);
                if (isset($result['vulnerabilities'])) {
                    $vulnerabilities = array_merge($vulnerabilities, $result['vulnerabilities']);
                }
            }
        }

        return $vulnerabilities;
    }

    private function fetchVulnerabilities($manufacturer, $product, $version, $cpeType) {
        if (!preg_match('/^\d/', $version)) {
            $version = "-";
        }
        $cpeMatchString = "cpe:2.3:$cpeType:$manufacturer:$product:$version";
        $url = "https://services.nvd.nist.gov/rest/json/cves/2.0?cpeName=" . $cpeMatchString;

        $headers = [
            'User-Agent' => 'PHP',
            'Accept'     => 'application/json',
            'Content-Type' => 'application/json',
            'apiKey'      => $this->apiKey
        ];

        return $this->fetcherService->fetch($url, $headers);
    }

    public function update($data) {
        // Handle updates from the API Coordinator
    }
}
