<?php

namespace App\Services;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\Storage;

class GoogleCloudService
{
    public function importFile()
    {
        $disk = Storage::disk('gcs');
    }

    /**
     * @throws FileNotFoundException
     */
    public function exportFile()
    {
        $disk = Storage::disk('gcs');
        dump($disk->get('test'));
    }
}
