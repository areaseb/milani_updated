<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTableEcProducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $hasColumns = Schema::hasColumns('ec_products', [
            'codice_cosma',
            'ean',
            'nome_amazon',
            'seo_amazon',
            'bullet_2',
            'bullet_3',
            'bullet_4',
            'bullet_5',
            'prodotti_corralati',
            'made_in',
            'image',
            'larghezza_scatola_collo_1',
            'larghezza_scatola_collo_2',
            'larghezza_scatola_collo_3',
            'larghezza_scatola_collo_4',
            'larghezza_scatola_collo_5',
            'profondita_scatola_collo_1',
            'profondita_scatola_collo_2',
            'profondita_scatola_collo_3',
            'profondita_scatola_collo_4',
            'profondita_scatola_collo_5',
            'altezza_scatola_collo_1',
            'altezza_scatola_collo_2',
            'altezza_scatola_collo_3',
            'altezza_scatola_collo_4',
            'altezza_scatola_collo_5',
            'cubatura',
            'peso_con_imballo_collo_1',
            'peso_con_imballo_collo_2',
            'peso_con_imballo_collo_3',
            'peso_con_imballo_collo_4',
            'peso_con_imballo_collo_5',
            'assemblato',
            'kit_e_istruzioni_incluse',
            'sku_set',
            'sku_parent'
        ]);

        if (!$hasColumns) {
            Schema::table('ec_products', function (Blueprint $table) {
                $table->string('codice_cosma')->nullable();
                $table->string('ean')->nullable();
                $table->string('nome_amazon')->nullable();
                $table->text('seo_amazon')->nullable();
                $table->text('bullet_2')->nullable();
                $table->text('bullet_3')->nullable();
                $table->text('bullet_4')->nullable();
                $table->text('bullet_5')->nullable();
                $table->text('prodotti_corralati')->nullable();
                $table->string('made_in')->nullable();
                $table->string('image')->nullable();
                $table->float('larghezza_scatola_collo_1', 6, 2)->nullable();
                $table->float('larghezza_scatola_collo_2', 6, 2)->nullable();
                $table->float('larghezza_scatola_collo_3', 6, 2)->nullable();
                $table->float('larghezza_scatola_collo_4', 6, 2)->nullable();
                $table->float('larghezza_scatola_collo_5', 6, 2)->nullable();
                $table->float('profondita_scatola_collo_1', 6, 2)->nullable();
                $table->float('profondita_scatola_collo_2', 6, 2)->nullable();
                $table->float('profondita_scatola_collo_3', 6, 2)->nullable();
                $table->float('profondita_scatola_collo_4', 6, 2)->nullable();
                $table->float('profondita_scatola_collo_5', 6, 2)->nullable();
                $table->float('altezza_scatola_collo_1', 6, 2)->nullable();
                $table->float('altezza_scatola_collo_2', 6, 2)->nullable();
                $table->float('altezza_scatola_collo_3', 6, 2)->nullable();
                $table->float('altezza_scatola_collo_4', 6, 2)->nullable();
                $table->float('altezza_scatola_collo_5', 6, 2)->nullable();
                $table->float('cubatura', 6, 2)->nullable();
                $table->float('peso_con_imballo_collo_1', 6, 2)->nullable();
                $table->float('peso_con_imballo_collo_2', 6, 2)->nullable();
                $table->float('peso_con_imballo_collo_3', 6, 2)->nullable();
                $table->float('peso_con_imballo_collo_4', 6, 2)->nullable();
                $table->float('peso_con_imballo_collo_5', 6, 2)->nullable();
                $table->string('assemblato')->nullable();
                $table->string('kit_e_istruzioni_incluse')->nullable();
                $table->text('sku_set')->nullable();
                $table->string('sku_parent')->nullable();
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
        //
    }
}
