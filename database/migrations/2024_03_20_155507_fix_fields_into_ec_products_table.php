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
        Schema::table('ec_products', function (Blueprint $table) {
            $table->text('nome_amazon')->change();
            $table->text('seo_amazon')->change();
            $table->text('assemblato')->change();
            $table->text('kit_e_istruzioni_incluse')->change();
            $table->text('sku_parent')->change();
            $table->text('barcode')->change();
            $table->text('carrier')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ec_products', function (Blueprint $table) {
            $table->string('nome_amazon')->change();
            $table->string('seo_amazon')->change();
            $table->string('assemblato')->change();
            $table->string('kit_e_istruzioni_incluse')->change();
            $table->string('sku_parent')->change();
            $table->string('barcode')->change();
            $table->string('carrier')->change();
        });
    }
};
