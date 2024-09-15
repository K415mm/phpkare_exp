<?php
require 'vendor/spyc/Spyc.php'; // Include the Spyc library

$icons = [
    'workstation' => 'workstation.png',
    'router' => 'router.png',
    'switch' => 'switch.png',
    'firewall' => 'firewall.png',
    'database' => 'database.png'
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nodes = isset($_POST['nodes']) ? json_decode($_POST['nodes'], true) : null;
    $connections = isset($_POST['connections']) ? json_decode($_POST['connections'], true) : null;
    $proj = $_POST['project'];
    $clientName = $_POST['client']; // Assuming client name is also sent in the POST request

    if (is_array($nodes) && is_array($connections)) {
        $formattedNodes = [];
        $formattedConnections = [];
        $vulnerabilitiesData = [];

        foreach ($nodes as $index => $node) {
            preg_match('/\((.*?)\)$/', $node['label'], $matches);
            $nodeType = $matches[1] ?? 'unknown';

            $formattedNodes[] = [
                'id' => $index + 1,
                'name' => htmlspecialchars($node['label']),
                'type' => htmlspecialchars($nodeType),
                'manufacturer' => htmlspecialchars($node['manufacturer']),
                'realName' => htmlspecialchars($node['realName']),
                'os' => htmlspecialchars($node['os']),
                'version' => htmlspecialchars($node['version']),
                'icon' => $icons[$nodeType] ?? 'default.png'
            ];

            // Collect vulnerabilities data
            $vulnerabilitiesData[] = [
                'id' => $index + 1,
                'vulnerabilities' => $node['vulnerabilities'] ?? []
            ];
        }

        foreach ($connections as $connection) {
            $formattedConnections[] = [
                'from' => (int)$connection['from'],
                'to' => (int)$connection['to'],
                'color' => $connection['color'],
                'width' => (int)$connection['width']
            ];
        }

        $yamlData = [
            'network' => [
                'nodes' => $formattedNodes,
                'connections' => $formattedConnections
            ]
        ];

        $yamlContent = Spyc::YAMLDump($yamlData);
        $projectDir = "data/$clientName/$proj";
        if (!is_dir($projectDir)) {
            mkdir($projectDir, 0777, true);
        }
        $yamlFile = "$projectDir/$proj.yaml";
        file_put_contents($yamlFile, $yamlContent);

        // Save vulnerabilities data as JSON
        $jsonFile = "$projectDir/{$proj}_vul.json";
        file_put_contents($jsonFile, json_encode($vulnerabilitiesData, JSON_PRETTY_PRINT));

        echo "Project saved successfully!";
    } else {
        echo "Invalid data format!";
    }
} else {
    echo "Invalid request method!";
}
?>
