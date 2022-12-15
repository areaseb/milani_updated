<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Image;

/**
 * @since 19/08/2015 08:05 AM
 */
class GoogleCloudService extends Controller
{
    public function importFile()
    {
        $files = Storage::disk('gcs')->files();

        foreach(array_chunk($files, 500) as $part){
            foreach ($part as $file) {
                Storage::disk('images')->put($file, $file);
            }
        }
    }

    public function resizeImages()
    {
        $files = Storage::disk('images')->files();
        $path = Storage::disk('images')->path('');
        foreach(array_chunk($files, 500) as $part){
            foreach ($part as $file) {
                $formatImage = rtrim($file, '.jpg');
                $img = Image::make("{$path}/{$formatImage}");
                dd($img);
            }
        }
    }
}
