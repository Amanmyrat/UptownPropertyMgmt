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
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->integer("property_id");
            $table->string("shortname")->nullable();
            $table->string("name")->nullable();
            $table->string("manager_name")->nullable();
            $table->boolean('is_active')->default(false);
            $table->string("property_type")->nullable();
            $table->string("email")->nullable();
            $table->integer("total_area")->nullable();
            $table->integer("total_units")->nullable();
            $table->string("market_rent")->nullable();
            $table->longText("current_rent")->nullable();
            $table->longText("vacancy_missing")->nullable();

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
        Schema::dropIfExists('properties');
    }
};
