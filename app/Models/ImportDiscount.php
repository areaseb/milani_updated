<?php

namespace App\Models;

use Botble\Ecommerce\Models\Product;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class ImportDiscount implements ToCollection
{
    public function collection(Collection $collection)
    {
        foreach ($collection as $row)
        {
            Product::where('sku', $row[0])->update([
                'sale_price' => $row[1],
            ]);
        }
    }
}
