# Define the project root directory
$projectRoot = "C:\laragon\www\cxsoar_net_map_24\cxsoar_netmap_enh\html-starter\horizontal-menu-template"

# Create the project structure

New-Item -ItemType Directory -Path "$projectRoot\microservices\UploaderService" -Force
New-Item -ItemType Directory -Path "$projectRoot\microservices\ValidatorService" -Force
New-Item -ItemType Directory -Path "$projectRoot\microservices\HandlerService" -Force
New-Item -ItemType Directory -Path "$projectRoot\microservices\FetcherService" -Force
New-Item -ItemType Directory -Path "$projectRoot\microservices\VisualizerService" -Force
New-Item -ItemType Directory -Path "$projectRoot\php_functions" -Force

# Create empty files

New-Item -ItemType File -Path "$projectRoot\routes.php" -Force
New-Item -ItemType File -Path "$projectRoot\openapi.yaml" -Force



# Create placeholder files for microservices
$microservices = @("UploaderService", "ValidatorService", "HandlerService", "FetcherService", "VisualizerService")
foreach ($service in $microservices) {
    $servicePath = "$projectRoot\microservices\$service\index.php"
    New-Item -ItemType File -Path $servicePath -Force
    Add-Content -Path $servicePath -Value "<?php`nnamespace PHPKare\Microservices;`nclass $service {`n    public function execute() {`n        // $service logic here`n    }`n}"
}

# Create placeholder files for php_functions
$phpFunctions = @("function1.php", "function2.php")
foreach ($function in $phpFunctions) {
    $functionPath = "$projectRoot\php_functions\$function"
    New-Item -ItemType File -Path $functionPath -Force
    Add-Content -Path $functionPath -Value "<?php`n// $function logic here"
}

Write-Output "Project structure created successfully!"
