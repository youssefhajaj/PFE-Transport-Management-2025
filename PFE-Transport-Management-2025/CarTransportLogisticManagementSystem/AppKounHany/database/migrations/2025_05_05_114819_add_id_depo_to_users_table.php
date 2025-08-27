<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Add nullable column first
            $table->unsignedBigInteger('id_depo')->nullable()->after('remember_token');
            
            // Add foreign key constraint
            $table->foreign('id_depo')
                  ->references('id')
                  ->on('depo')
                  ->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['id_depo']);
            $table->dropColumn('id_depo');
        });
    }
};