<?php

namespace PHPKare\Microservices\{{type}};

use PHPKare\ApiCoordinator;
use PHPKare\ApiEventInterface;
use PHPKare\Microservices\{{type}}\{{microservice}};

class {{service}} extends {{microservice}}
{
    public function execute()
    {
        // {{service}} logic here
    }

    public function update(ApiEventInterface $event)
    {
        // Handle API events as needed
        // ...
    }
}