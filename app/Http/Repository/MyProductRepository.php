<?php

namespace App\Http\Repository;

use Botble\Ecommerce\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class MyProductRepository
{

    public function updateProduct(object $data): Model|Product|Builder
    {
        $product = Product::where('sku', $data['sku'])->firstOrFail();
        if ($product) {
            $product->update($data->toArray());
            $product->save();
            return $product;
        }
        return $product;
    }
}
