<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('transports', function (Blueprint $table) {
            // New foreign key fields
            $table->unsignedBigInteger('name_id')->nullable()->after('chassis');
            $table->unsignedBigInteger('societe_id')->nullable()->after('name_id');
            $table->unsignedBigInteger('tree_id')->nullable()->after('societe_id');

            // Foreign key constraints
            $table->foreign('name_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('societe_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('tree_id')->references('id')->on('users')->onDelete('set null');

            // Optionally drop old columns
            $table->dropColumn(['name', 'societe', 'tree']);
        });
    }

    public function down()
    {
        Schema::table('transports', function (Blueprint $table) {
            // Revert changes
            $table->dropForeign(['name_id']);
            $table->dropForeign(['societe_id']);
            $table->dropForeign(['tree_id']);
            
            $table->dropColumn(['name_id', 'societe_id', 'tree_id']);

            // Restore old columns
            $table->string('name')->nullable(false);
            $table->string('societe')->nullable();
            $table->string('tree')->nullable();
        });
    }

};
