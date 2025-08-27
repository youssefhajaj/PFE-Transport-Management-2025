<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('isMetteurAuMain')->default(0)->after('isLogistic');
            $table->string('depo')->nullable()->after('id_depo');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('isMetteurAuMain');
            $table->dropColumn('depo');
        });
    }
};
