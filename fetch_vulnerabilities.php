<?php
require 'vendor/autoload.php';

use PHPKare\Microservices\external_in_out\FetchVulnerabilitiesService;

if (isset($_GET['manufacturer']) && isset($_GET['product']) && isset($_GET['version']) && isset($_GET['type'])) {
    $manufacturer = $_GET['manufacturer'];
    $product = $_GET['product'];
    $version = $_GET['version'];
    $type = $_GET['type'];

    $service = new FetchVulnerabilitiesService();
    $vulnerabilities = $service->fetch($manufacturer, $product, $version, $type);

    header('Content-Type: application/json');
    echo json_encode(['vulnerabilities' => $vulnerabilities]);
} else {
    echo json_encode(['error' => 'Missing parameters']);
}
