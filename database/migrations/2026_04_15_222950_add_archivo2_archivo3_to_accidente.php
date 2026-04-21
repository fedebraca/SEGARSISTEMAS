<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddArchivo2Archivo3ToAccidente extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('accidente', function (Blueprint $table) {
            $table->string('archivo2')->nullable()->default('')->after('archivo');
            $table->string('archivo3')->nullable()->default('')->after('archivo2');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('accidente', function (Blueprint $table) {
            $table->dropColumn(['archivo2', 'archivo3']);
        });
    }
}
