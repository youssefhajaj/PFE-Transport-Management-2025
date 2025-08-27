<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('transports', function (Blueprint $table) {
            // Add the column (nullable first to avoid issues with existing data)
            $table->unsignedBigInteger('id_depo')->nullable()->after('societe_id');
            
            // Add foreign key constraint
            $table->foreign('id_depo')
                  ->references('id')
                  ->on('depo')
                  ->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('transports', function (Blueprint $table) {
            // Drop foreign key first
            $table->dropForeign(['id_depo']);
            
            // Then drop the column
            $table->dropColumn('id_depo');
        });
    }
};