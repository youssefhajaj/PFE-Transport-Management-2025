<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('transports', function (Blueprint $table) {
            $table->float('kilometrage', 8, 2)->default(0)->change();
        });
    }

    public function down()
    {
        Schema::table('transports', function (Blueprint $table) {
            $table->integer('kilometrage')->default(0)->change();
        });
    }
};
