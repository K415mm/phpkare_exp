<?php

namespace PHPKare\Microservices;

class HandlerService
{
    public function processCSV($file)
    {
        $data = [];
        $headers = fgetcsv($file);
        while ($row = fgetcsv($file)) {
            $data[] = array_combine($headers, $row);
        }
        return $data;
    }
}
