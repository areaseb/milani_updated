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
        Schema::table('ec_order_addresses', function (Blueprint $table) {
            $table->string('vat', 15)->nullable();
            $table->string('pec', 191)->nullable();
            $table->string('sdi', 7)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ec_order_addresses', function (Blueprint $table) {
            $table->dropColumn('vat');
            $table->dropColumn('pec');
            $table->dropColumn('sdi');
        });
    }
};
