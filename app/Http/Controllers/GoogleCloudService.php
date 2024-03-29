<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Symfony\Component\HttpFoundation\Response;

/**
 * @since 19/08/2015 08:05 AM
 */
class GoogleCloudService extends Controller
{
    /**
     * @throws FileNotFoundException
     */
    public function importImage(): void
    {
        $filesName = Storage::disk('gcs')->files();
        $path = Storage::disk('products')->path('');

        $i = 0;
        foreach(array_chunk($filesName, 500) as $part){
            foreach ($part as $file) {
                if ($i == 2) {
                    die();
                }
                $fileContent = Storage::disk('gcs')->get($file);

                $img = Image::make($fileContent);

                $img_150 = Image::make($fileContent)->resize(150, 150);
                $img_400 = Image::make($fileContent)->resize(400, 400);
                $img_800 = Image::make($fileContent)->resize(800, 800);

                $img->save($path.$file, 80);

                $fileName = rtrim($file, '.jpg');

                $img_150->save($path.$fileName.'-150x150.jpg', 80);
                $img_400->save($path.$fileName.'-400x400.jpg', 80);
                $img_800->save($path.$fileName.'-800x800.jpg', 80);

                $this->saveImageInDatabase($img, $file);
                $i++;
            }
        }
    }

    public function saveImageInDatabase($image, $imageName): \Illuminate\Http\Response|Application|ResponseFactory
    {
        $disk = Storage::disk('gcs');
        list($imageNameClean, $ext) = explode('.', $imageName);
        $productImage = DB::table('media_files')->where('name', $imageNameClean);
        $lastModified = Carbon::createFromTimestamp($disk->lastModified($imageName));
        if ($productImage->first()) {
            if($productImage->where('updated_at', '<>', $lastModified)->exists())
            {
                $insertImage = DB::table('media_files')->where('name', $imageNameClean)->update([
                    'user_id' => 0,
                    'name' => $imageNameClean,
                    'folder_id' => 3,
                    'mime_type' => 'image/'.$ext,
                    'size' => $image->filesize(),
                    'url' => 'products/'.$imageName,
                    'updated_at' => $lastModified
                ]);
                return response($insertImage, Response::HTTP_OK);
            }
        }
        else {
            $insertImage = DB::table('media_files')->insert([
                'user_id' => 0,
                'name' => $imageNameClean,
                'folder_id' => 3,
                'mime_type' => 'image/'.$ext,
                'size' => $image->filesize(),
                'url' => 'products/'.$imageName,
                'options' => '[]',
                'created_at' => $lastModified,
                'updated_at' => $lastModified
            ]);

            if (!$insertImage) {
                return response('Can\'t inserted data in to database', Response::HTTP_BAD_REQUEST);
            }
            return response($insertImage, Response::HTTP_OK);
        }

        return response(Response::HTTP_BAD_REQUEST);
    }
}
