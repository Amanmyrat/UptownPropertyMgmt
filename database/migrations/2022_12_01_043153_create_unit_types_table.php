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
        Schema::create('unit_types', function (Blueprint $table) {
            $table->id('UnitTypeId');
            $table->string('Name')->nullable();
            $table->string('Comment')->nullable();
            $table->double('Bathrooms')->nullable();
            $table->datetime('CreateDate',0)->nullable();
            $table->integer('CreateUserId')->nullable();
            $table->datetime('UpdateDate',0)->nullable();
            $table->integer('UpdateUserId')->nullable();
            $table->integer('Bedrooms')->nullable();
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
        Schema::dropIfExists('unit_types');
    }
};
