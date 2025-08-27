<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('transports', function (Blueprint $table) {
            $table->string('societe')->nullable();
            $table->integer('is_created_by_logistic')->default(0);
            $table->string('etat_commentaire')->nullable();
        });
    }

    public function down()
    {
        Schema::table('transports', function (Blueprint $table) {
            $table->dropColumn(['societe', 'is_created_by_logistic', 'etat_commentaire']);
        });
    }
};
