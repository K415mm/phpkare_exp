<?php

namespace PHPKare\Microservices\{{type}};

use PHPKare\ApiCoordinator;
use PHPKare\ApiEventInterface;

class {{name}}
{
    private $apiCoordinator;

    public function __construct(ApiCoordinator $apiCoordinator)
    {
        $this->apiCoordinator = $apiCoordinator;
        $this->apiCoordinator->attach($this);
    }

    public function execute()
    {
        // {{name}} logic here
    }

    public function update(ApiEventInterface $event)
    {
        // Handle API events as needed 
        // ...
    }
}