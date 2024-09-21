#!/usr/bin/env php
<?php

require 'vendor/autoload.php';

// Define templates
$microserviceTemplate = file_get_contents('templates/microservice.php');
$serviceTemplate = file_get_contents( 'templates/service.php');
$apiCoordinatorTemplate = file_get_contents('templates/api_coordinator.php');

// Define default microservices
$defaultMicroservices = [
    'Internal' => [
        "UploaderService", "ValidatorService", "HandlerService", "FetcherService", "VisualizerService"
    ],
    'External_In' => [],
    'External_Out' => [],
    'External_In_Out' => ["FetchVulnerabilitiesService"]
];

// Function to create directories
function createDirectory($path) {
    if (!file_exists($path)) {
        mkdir($path, 0777, true);
        echo "Created directory: $path\n";
    }
}

// Function to create files
function createFile($path, $content = '') {
    if (!file_exists($path)) {
        file_put_contents($path, $content);
        echo "Created file: $path\n";
    }
}

// Function to add a microservice
function addMicroservice($name, $type, $api = false) {
    $servicePath = "Microservices/$type/$name";
    createDirectory($servicePath);
    $indexPath = "$servicePath/index.php";
    $content = str_replace(
        ['{{name}}', '{{type}}', '{{api}}'],
        [$name, $type, $api ? 'true' : 'false'],
        $microserviceTemplate
    );
    createFile($indexPath, $content);
    if ($api) {
        createDirectory("$servicePath/service");
        createFile("$servicePath/service/APIClient.php", str_replace('{{name}}', $name, $apiCoordinatorTemplate));
    }
}

// Function to add a service
function addService($microservice, $service, $type, $api = false) {
    $servicePath = "Microservices/$type/$microservice/service/$service.php";
    createDirectory(dirname($servicePath));
    $content = str_replace(
        ['{{microservice}}', '{{service}}', '{{type}}', '{{api}}'],
        [$microservice, $service, $type, $api ? 'true' : 'false'],
        $serviceTemplate
    );
    createFile($servicePath, $content);
    if ($api) {
        createDirectory("$servicePath/api");
        createFile("$servicePath/api/APIClient.php", str_replace('{{name}}', $service, $apiCoordinatorTemplate));
    }
}

// Function to initialize a PHPKare project
function initializeProject() {
    global $defaultMicroservices;
    $types = array_keys($defaultMicroservices);
    foreach ($types as $type) {
        foreach ($defaultMicroservices[$type] as $service) {
            addMicroservice($service, $type);
        }
    }
    createDirectory("Functions");
    createFile("routes.php");
    createFile("openapi.yaml");
    echo "Project structure created successfully!\n";
}

if ($argc < 2) {
    echo "Usage: phpkarec <command> [arguments]\n";
    exit(1);
}

$command = $argv[1];

switch ($command) {
    case 'init':
        initializeProject();
        break;
    case 'add':
        if ($argc < 4) {
            echo "Usage: phpkarec add <type> <name> [--api]\n";
            exit(1);
        }
        $type = $argv[2];
        $name = $argv[3];
        $api = isset($argv[4]) && $argv[4] === '--api';
        if ($type === 'micro') {
            addMicroservice($name, 'Internal', $api);
        } elseif ($type === 'function') {
            createFile("Functions/$name.php");
        } else {
            echo "Unknown type: $type\n";
            exit(1);
        }
        break;
    case 'add-service':
        if ($argc < 5) {
            echo "Usage: phpkarec add-service <microservice> <service> <subtype> [--api]\n";
            exit(1);
        }
        $microservice = $argv[2];
        $service = $argv[3];
        $subtype = $argv[4];
        $api = isset($argv[5]) && $argv[5] === '--api';
        addService($microservice, $service, $subtype, $api);
        break;
    default:
        echo "Unknown command: $command\n";
        exit(1);
}

?>