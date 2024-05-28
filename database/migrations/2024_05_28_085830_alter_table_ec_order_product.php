<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasColumn('ec_order_product', 'external_sku')) {
            Schema::table('ec_order_product', function (Blueprint $table) {
                $table->text('external_sku')->after('product_id')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ec_order_product', function (Blueprint $table) {
            $table->dropColumn('external_sku');
        });
    }
};
