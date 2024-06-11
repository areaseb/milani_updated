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
        if (! Schema::hasColumn('ec_order_product', 'parentela_amz')) {
            Schema::table('ec_order_product', function (Blueprint $table) {
				/*
				SQL QUERY:

				ALTER TABLE `ec_products` ADD `parentela` VARCHAR(32) NULL AFTER `data_arrivo`, ADD `sku_parentela` VARCHAR(191) NULL AFTER `parentela`, ADD `parentela_amz` VARCHAR(32) NULL AFTER `sku_parentela`, ADD `sku_parentela_amz` VARCHAR(191) NULL AFTER `parentela_amz`, ADD `tipo_relazione_amz` VARCHAR(32) NULL AFTER `sku_parentela_amz`, ADD `tema_relazione_amz` TEXT NULL AFTER `tipo_relazione_amz`, ADD `attaccati_amz` TEXT NULL AFTER `tema_relazione_amz`, ADD `cont_legno` BOOLEAN NULL AFTER `attaccati_amz`, ADD `cod_fsc` VARCHAR(191) NULL AFTER `cont_legno`, ADD `alt_seduta` VARCHAR(191) NULL AFTER `cod_fsc`, ADD `n_cassetti` INT NULL AFTER `alt_seduta`, ADD `n_ripiani` INT NULL AFTER `n_cassetti`; 
				*/

				/*
				PARENTELA	SKU_PARENTELA	PARENTELA_AMZ	SKU_PARENTELA_AMZ	TIPO RELAZIONE_AMZ	TEMA RELAZIONE AMZ	ATTACCATI AMZ	CONT_LEGNO	COD_FSC	ALT_SEDUTA	N_CASSETTI	N_RIPIANI

				parentela (parent, child)
				sku_parentela
				parentela_amz
				sku_parentela_amz
				tipo_relazione_amz (variation, null)
				tema_relazione_amz (SizeName-ColorName)
				attaccati_amz (a, b, c, // a, b, c,)
				cont_legno (Sì, No)
				cod_fsc
				alt_seduta
				n_cassetti
				n_ripiani
				*/
				$table->string('parentela')->nullable();
				$table->string('parentela_amz')->nullable();
				$table->string('parentela_amz')->nullable();
				$table->string('sku_parentela_amz')->nullable();
				$table->string('tipo_relazione_amz')->nullable();
				$table->string('tema_relazione_amz')->nullable();
				$table->string('attaccati_amz')->nullable();
				$table->boolean('cont_legno')->nullable();
				$table->string('cod_fsc')->nullable();
				$table->float('alt_seduta', 6, 2)->nullable();
				$table->float('n_cassetti', 6, 2)->nullable();
				$table->float('n_ripiani', 6, 2)->nullable();
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

    }
};
