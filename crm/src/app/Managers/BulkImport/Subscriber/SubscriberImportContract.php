<?php


namespace App\Managers\BulkImport\Subscriber;


use Illuminate\Http\UploadedFile;

interface SubscriberImportContract
{
    public function read() : array;

    public function sanitize($subscribers);
}
