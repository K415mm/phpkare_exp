<?php

namespace PHPKare\Microservices;

class UploaderService
{
    public function upload($file)
    {
        // Handle file upload
        $allowedMimeTypes = ['text/csv', 'application/vnd.ms-excel', 'text/plain', 'text/x-csv', 'text/comma-separated-values', 'application/csv', 'application/excel', 'application/vnd.msexcel', 'application/vnd.ms-excel', 'application/vnd.ms-excel', 'application/x-csv', 'application/x-excel', 'application/x-msexcel', 'text/x-comma-separated-values', 'text/x-comma-separated-values', 'text/tab-separated-values'];
        $fileType = mime_content_type($file['tmp_name']);

        if (in_array($fileType, $allowedMimeTypes) && $file['error'] == UPLOAD_ERR_OK && is_uploaded_file($file['tmp_name'])) {
            return fopen($file['tmp_name'], 'r');
        } else {
            throw new \Exception("File upload failed!");
        }
    }
}
