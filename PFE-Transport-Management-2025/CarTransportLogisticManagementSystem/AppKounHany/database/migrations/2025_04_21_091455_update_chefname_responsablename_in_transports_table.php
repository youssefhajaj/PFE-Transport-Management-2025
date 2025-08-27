<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::table('transports', function (Blueprint $table) {
        // Optional: drop old string fields
        $table->dropColumn('chefname');
        $table->dropColumn('responsablename');

        // Add foreign keys (nullable)
        $table->unsignedBigInteger('chefname_id')->nullable()->after('chefvalid');
        $table->unsignedBigInteger('responsablename_id')->nullable()->after('responsablevalid');

        $table->foreign('chefname_id')->references('id')->on('users')->onDelete('set null');
        $table->foreign('responsablename_id')->references('id')->on('users')->onDelete('set null');
    });
}

public function down(): void
{
    Schema::table('transports', function (Blueprint $table) {
        $table->dropForeign(['chefname_id']);
        $table->dropForeign(['responsablename_id']);
        $table->dropColumn('chefname_id');
        $table->dropColumn('responsablename_id');

        $table->string('chefname')->nullable();
        $table->string('responsablename')->nullable();
    });
}

};
