<?php

namespace PHPKare;

class MicroserviceFactory
{
    public static function create($type)
    {
        switch ($type) {
            case 'uploader':
                return new Microservices\UploaderService();
            case 'validator':
                return new Microservices\ValidatorService();
            case 'handler':
                return new Microservices\HandlerService();
            case 'fetcher':
                return new Microservices\FetcherService();
            case 'visualizer':
                return new Microservices\VisualizerService();
            default:
                throw new \Exception("Invalid service type");
        }
    }
}
