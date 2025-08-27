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
        $table->string('BL')->nullable()->after('prestataire'); // Adjust position if needed
    });
}

public function down()
{
    Schema::table('transports', function (Blueprint $table) {
        $table->dropColumn('BL');
    });
}

};
