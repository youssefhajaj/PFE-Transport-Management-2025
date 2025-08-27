<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::table('transports', function (Blueprint $table) {
        $table->timestamp('bl_sent_at')->nullable()->after('BL');
    });
}

public function down()
{
    Schema::table('transports', function (Blueprint $table) {
        $table->dropColumn('bl_sent_at');
    });
}
};
