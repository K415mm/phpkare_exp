<?php

namespace PHPKare\Microservices;

class ValidatorService
{
    public function validateNodes($nodes)
    {
        $requiredFields = ['id', 'name', 'type', 'manufacturer', 'realName', 'os', 'version'];
        foreach ($nodes as $node) {
            foreach ($requiredFields as $field) {
                if (!isset($node[$field])) {
                    return false;
                }
            }
        }
        return true;
    }

    public function validateConnections($connections)
    {
        $requiredFields = ['from', 'to', 'color', 'width'];
        foreach ($connections as $connection) {
            foreach ($requiredFields as $field) {
                if (!isset($connection[$field])) {
                    return false;
                }
            }
        }
        return true;
    }
}
