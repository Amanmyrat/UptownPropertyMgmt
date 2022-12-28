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
        Schema::create('units', function (Blueprint $table) {
            $table->id('UnitID');
            $table->integer('PropertyID')->default(0);
            $table->string('Name')->nullable();
            $table->text('Comment');
            $table->integer('UnitTypeID')->default(0);
            $table->integer('SquareFootage')->default(0);
            $table->integer('MaxOccupancy')->default(0);
            $table->integer('Bedrooms')->default(0);
            $table->double('Bathrooms')->default(0); //double
            $table->integer('SortOrder')->default(0);
            $table->datetime('CreateDate',0);
            $table->integer('CreateUserID')->default(0);
            $table->datetime('UpdateDate',0);
            $table->integer('UpdateUserID')->default(0);
            $table->string('OnlineListingsUsage')->nullable();
            $table->integer('FloorID')->nullable();
            $table->integer('ColorID')->nullable();
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
        Schema::dropIfExists('units');
    }
};
