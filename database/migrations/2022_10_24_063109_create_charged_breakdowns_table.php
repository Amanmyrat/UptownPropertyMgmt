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
        Schema::create('charged_breakdowns', function (Blueprint $table) {
            $table->id();
            $table->integer('CustomersCustomerDisplayID');
            $table->integer('EntitiesEntityID');
            $table->string('SubEntitiesName');
            $table->double("Total");
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
        Schema::dropIfExists('charged_breakdowns');
    }
};
