<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration
{
    public function up(): void
    {
        Schema::create('ec_products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->longText('content')->nullable();
            $table->string('status', 60)->default('published');
            $table->text('images')->nullable();
            $table->string('sku')->nullable();
            $table->integer('order')->unsigned()->default(0);
            $table->integer('quantity')->unsigned()->nullable();
            $table->tinyInteger('allow_checkout_when_out_of_stock')->unsigned()->default(0);
            $table->tinyInteger('with_storehouse_management')->unsigned()->default(0);
            $table->tinyInteger('is_featured')->unsigned()->default(0);
            $table->text('options')->nullable();
            $table->integer('category_id')->unsigned()->nullable();
            $table->integer('brand_id')->unsigned()->nullable();
            $table->tinyInteger('is_variation')->default(0);
            $table->tinyInteger('is_searchable')->default(0);
            $table->tinyInteger('is_show_on_list')->default(0);
            $table->tinyInteger('sale_type')->default(0);
            $table->double('price')->unsigned()->nullable();
            $table->double('sale_price')->unsigned()->nullable();
            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();
            $table->float('length')->nullable();
            $table->float('wide')->nullable();
            $table->float('height')->nullable();
            $table->float('weight')->nullable();
            $table->string('barcode')->nullable();
            $table->string('stock_status')->nullable();
            $table->string('product_type')->nullable();
            $table->integer('created_by_id')->nullable()->default(0);
            $table->string('created_by_type', 255)->default(addslashes(User::class));
            $table->string('length_unit', 20)->nullable();
            $table->string('wide_unit', 20)->nullable();
            $table->string('height_unit', 20)->nullable();
            $table->string('weight_unit', 20)->nullable();
            $table->index(['brand_id', 'status', 'is_variation', 'created_at']);
            $table->integer('tax_id')->unsigned()->nullable();
            $table->bigInteger('views')->default(0);
            $table->timestamps();
        });

        if (!Schema::hasColumns('ec_products', [
            'codice_cosma',
            'ean',
            'nome_amazon',
            'seo_amazon',
            'bullet_1',
            'bullet_2',
            'bullet_3',
            'bullet_4',
            'bullet_5',
            'prodotti_corralati',
            'made_in',
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
        ])) {
            Schema::table('ec_products', function (Blueprint $table) {
                $table->string('codice_cosma')->nullable();
                $table->string('ean')->nullable();
                $table->string('nome_amazon')->nullable();
                $table->text('seo_amazon')->nullable();
                $table->text('bullet_1')->nullable();
                $table->text('bullet_2')->nullable();
                $table->text('bullet_3')->nullable();
                $table->text('bullet_4')->nullable();
                $table->text('bullet_5')->nullable();
                $table->text('prodotti_corralati')->nullable();
                $table->string('made_in')->nullable();
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

    public function down(): void
    {
        //
    }
};

