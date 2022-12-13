<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;


class ImportController extends Controller
{

    public function productMediaImporter()
    {

        $files = Storage::disk('public')->allFiles('test');

        dump($files);

    }

}
