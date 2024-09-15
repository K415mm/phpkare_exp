#!/usr/bin/env php
<?php

require 'vendor/autoload.php';



function createDirectory($path) {
    if (!file_exists($path)) {
        mkdir($path, 0777, true);
        echo "Created directory: $path\n";
    }
}

function createFile($path, $content = '') {
    if (!file_exists($path)) {
        file_put_contents($path, $content);
        echo "Created file: $path\n";
    }
}

function addMicroservice($name, $type) {
    $servicePath = "Microservices/$type/$name";
    createDirectory($servicePath);
    $indexPath = "$servicePath/index.php";
    $content = "<?php\nnamespace PHPKare\\Microservices\\$type;\nclass $name {\n    public function execute() {\n        // $name logic here\n    }\n}\n";
    createFile($indexPath, $content);
}

function addFunction($name) {
    $functionPath = "/Functions/$name.php";
    $content = "<?php\n// $name logic here\n";
    createFile($functionPath, $content);
}

function addService($microservice, $service, $type) {
    $servicePath = "/Microservices/$type/$microservice/$service.php";
    $content = "<?php\nnamespace PHPKare\\Microservices\\$type;\n\nuse PHPKare\\Microservices\\$type\\$microservice;\n\nclass $service extends $microservice {\n    public function execute() {\n        // $service logic here\n    }\n}\n";
    createFile($servicePath, $content);
}

function initializeProject() {
    $types = ['Internal', 'External_In', 'External_out', 'External_In_Out'];
    $microservices = [
        'Internal' => ["UploaderService", "ValidatorService", "HandlerService", "FetcherService", "VisualizerService"],
        'External_In' => [],
        'External_Out' => [],
        'External_In_Out' => ["FetchVulnerabilitiesService"]
    ];

    foreach ($types as $type) {
        foreach ($microservices[$type] as $service) {
            addMicroservice($service, $type);
        }
    }

    createDirectory("/Functions");
    createFile("/routes.php");
    createFile("/openapi.yaml");

    // Add API Coordinator
    addMicroservice('APICoordinator', 'External');

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
        if ($argc < 5) {
            echo "Usage: phpkarec add <type> <name> <subtype>\n";
            exit(1);
        }
        $type = $argv[2];
        $name = $argv[3];
        $subtype = $argv[4];
        if ($type === 'micro') {
            addMicroservice($name, $subtype);
        } elseif ($type === 'function') {
            addFunction($name);
        } else {
            echo "Unknown type: $type\n";
            exit(1);
        }
        break;
    case 'add-service':
        if ($argc < 6) {
            echo "Usage: phpkarec add-service <microservice> <service> <subtype>\n";
            exit(1);
        }
        $microservice = $argv[2];
        $service = $argv[3];
        $subtype = $argv[4];
        addService($microservice, $service, $subtype);
        break;
    default:
        echo "Unknown command: $command\n";
        exit(1);
}

?>
