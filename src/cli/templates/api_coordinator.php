<?php

namespace PHPKare\Microservices\{{name}};

use PHPKare\ApiCoordinator;
use PHPKare\ApiEventInterface;

class APIClient
{
    private $apiCoordinator;

    public function __construct(ApiCoordinator $apiCoordinator)
    {
        $this->apiCoordinator = $apiCoordinator;
        $this->apiCoordinator->attach($this);
    }

    public function update(ApiEventInterface $event)
    {
        // Handle API events as needed
        // ...
    }
}