<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('unit_availabilities', function (Blueprint $table) {
            $table->id();
            $table->string("description")->nullable();
            $table->string("unitname")->nullable();
            $table->double("marketrent")->default(0);
            $table->string("unitstatuscomment")->nullable();
            $table->integer("entityid")->default(0);
            $table->integer("statusId")->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('unit_availabilities');
    }
};
