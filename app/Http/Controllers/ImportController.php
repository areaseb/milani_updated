<?php

namespace App\Http\Controllers;

use Botble\Media\Http\Resources\FileResource;
use Botble\Media\Http\Resources\FolderResource;
use Botble\Media\Models\MediaFile;
use Botble\Media\Models\MediaFolder;
use Botble\Media\Repositories\Interfaces\MediaFileInterface;
use Botble\Media\Repositories\Interfaces\MediaFolderInterface;
use Botble\Media\Repositories\Interfaces\MediaSettingInterface;
use Botble\Media\Services\UploadsManager;
use Botble\Media\Supports\Zipper;
use Carbon\Carbon;
use Eloquent;
use Exception;
use Illuminate\Support\Facades\File;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use RvMedia;
use Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Throwable;

/**
 * @since 19/08/2015 08:05 AM
 */
class ImportController extends Controller
{

    public function productMediaImporter() {

        $files = Storage::disk('public')->allFiles('test');

        return $files;
    }

}
