<?php

namespace App\Models;

use Carbon\Carbon;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;


class GoogleMedia
{
    public function __construct()
    {
        $this->disk = Storage::disk('gcs');
    }

    /**
     * process images with sku/skus
     * @param [array] $skus
     */
    public function setMediaProcessing($skus)
    {
        foreach($this->setMediaFinder($skus) as $file)
        {
            $product = $this->findProductFromFile($file);

            if($product)
            {

                if($product->media()->where('original', $file)->count() == 0)
                {

                    Log::info('doing new image '.$file);
                    $this->processImage($product, $file);
                    Log::info('new image added '.$file);
                }
                else
                {
                    $lastModified = Carbon::createFromTimestamp($this->disk->lastModified($file));
                    if($product->media()->where('original', $file)->where('created_at', '<>', $lastModified)->exists())
                    {
                        $product->media()->where('original', $file)->where('created_at', '<>', $lastModified)->first()->delete();
                        $this->processImage($product, $file);
                        Log::info('new image updated '.$file);
                    }
                }
            }
            else
            {
                Log::info('product not found for file '.$file);
            }
        }
    }

    /**
     * return files containing sku
     * @param [array] $skus
     */
    public function setMediaFinder($skus)
    {
        return collect($this->disk->allFiles('/'))
            ->filter(function ($file) use ($skus){
                return Str::contains($file, $skus);
            });
    }


    /**
     * return product having sku
     * @param  [string] $file               [description]
     * @return [model]  $product
     */
    public function findProductFromFile($file)
    {
        $allProductCodes = Product::codeArray();

        if(Str::endsWith($file, '.jpg'))
        {
            $code = Str::replace('.jpg', '', $file);
            if( isset($allProductCodes[$code]) )
            {
                return Product::find($allProductCodes[$code]);
            }

            $exploded = explode('_', $file);
            $code = $exploded[0];

            if( isset($allProductCodes[$code]) )
            {
                return Product::find($allProductCodes[$code]);
            }
        }


        return null;
    }



    public function processImage($product, $file)
    {
        $count = $product->media->count()+1;
        $response = $this->disk->get($file);
        if($response)
        {
            $filename = Str::random(20).'.jpg';
            //put in /storage/app/public/pruducts/original/
            Storage::disk('products')->put('original/'.$filename, $response);
            $data = $this->saveInSizes($filename);

            $data += [
                'mime' => 'image',
                'filename' => $filename,
                'original' => $file,
                'description' => $product->name . ' '.$count, // alt field, (we cound add category, color, material, to make it more SEO friendly)
                'mediable_id' => $product->id, // id of model associated
                'mediable_type' => get_class($product), // model associated
                'media_order' => $count, // order
            ];

            //save media in DB
            Media::create($data);
            Log::info('added media '.$product->id.PHP_EOL);

            Storage::disk('products')->delete('original/'.$filename);
        }
        else
        {
            Log::error('Could not process '.$product->id);
        }
    }


    /*
    args: str filename
    return [] with media info
    more about image intervention functions at https://image.intervention.io/v2
    */
    public function saveInSizes($filename) : array
    {
        //create the image obj
        $img = Image::make( Storage::disk('products')->path('original/'.$filename) );

        $width = $img->width();
        $height = $img->height();
        $size = round($img->filesize()/1000);

        $img->backup();

        //resize x: auto, y:1000px, mantaining aspectRatio
        $img->resize(null, 1000, function ($constraint) {
            $constraint->aspectRatio();
        });
        $img->save( Storage::disk('products')->path('full/'.$filename) );
        //reset forgets the last risize and brings you back to $img->backup(), the original object
        $img->reset();

        //fit is a resize and crop
        $img->fit(360,360);
        $img->save(  Storage::disk('products')->path('display/'.$filename)  );
        $img->reset();


        $img->fit(109, 109);
        $img->save(  Storage::disk('products')->path('thumb/'.$filename)  );

        unset($img);

        return [
            'width' => $width,
            'height' => $height,
            'size' => $size
        ];
    }


}
