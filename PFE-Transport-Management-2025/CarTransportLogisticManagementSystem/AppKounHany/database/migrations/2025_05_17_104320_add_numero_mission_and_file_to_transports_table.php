<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('transports', function (Blueprint $table) {
            $table->string('numero_mission')->nullable()->after('id_depo');
            $table->string('file_path')->nullable()->after('numero_mission');
        });
    }

    public function down()
    {
        Schema::table('transports', function (Blueprint $table) {
            $table->dropColumn(['numero_mission', 'file_path']);
        });
    }
};
