<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('transports', function (Blueprint $table) {
            $table->string('disponibilite')->nullable()->after('etatavancement');
            $table->text('commentaire')->nullable()->after('disponibilite');
        });
    }

    public function down()
    {
        Schema::table('transports', function (Blueprint $table) {
            $table->dropColumn('disponibilite');
            $table->dropColumn('commentaire');
        });
    }
};