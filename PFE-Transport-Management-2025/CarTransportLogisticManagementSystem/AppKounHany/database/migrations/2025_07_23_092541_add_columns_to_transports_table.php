<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('transports', function (Blueprint $table) {
            $table->integer('retard')->default(0);
            $table->string('typevehicule')->nullable();
            $table->integer('kilometrage')->default(0);
            $table->string('zone')->nullable();
            $table->integer('roulette')->default(0);
        });
    }

    public function down()
    {
        Schema::table('transports', function (Blueprint $table) {
            $table->dropColumn(['retard', 'typevehicule', 'kilometrage', 'zone', 'roulette']);
        });
    }
};
