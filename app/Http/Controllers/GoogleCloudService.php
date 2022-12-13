<?php

namespace App\Http\Controllers;


use Illuminate\Support\Facades\Storage;

/**
 * @since 19/08/2015 08:05 AM
 */
class GoogleCloudService extends Controller
{
    public $cloud;

    public function __construct(Storage $disk)
    {
        $this->cloud = $disk;
    }

    public function importFile()
    {
        dd($this->cloud);
    }

    public function exportFile()
    {

    }
}
