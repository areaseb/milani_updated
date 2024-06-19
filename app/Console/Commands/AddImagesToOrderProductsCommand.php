<?php

namespace App\Console\Commands;

use Botble\Ecommerce\Models\OrderProduct;
use Botble\Ecommerce\Models\Product;
use Illuminate\Console\Command;

class AddImagesToOrderProductsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'order:product_image';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Attach images to order\'s products';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $order_products = OrderProduct::whereNull('product_image')->whereNotNull('product_id')->get();

        foreach($order_products as $order_product) {
            $product = Product::find($order_product->product_id);

            if($product) {          
                $image = null;

                if($product->image) {
                    $image = $product->image;
                } else if($product->images && is_array($product->images)) {
                    $image = $product->images[0];
                }

                $order_product->product_image = $image;
                $order_product->save();
            }
        }

        return Command::SUCCESS;
    }
}
