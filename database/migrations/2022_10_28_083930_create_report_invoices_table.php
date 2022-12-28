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
        Schema::create('report_invoices', function (Blueprint $table) {
            $table->id();
            $table->string("AccountsName");
            $table->integer("CustomersCustomerDisplayID")->default(0);
            $table->string("SubEntitiesName")->nullable();
            $table->string("UnitTypesName")->nullable();
            $table->double("Total")->default(0);
            $table->integer("CustomersCustomerID")->default(0);
            $table->integer("EntitiesEntityID")->default(0);
            $table->integer("SubEntitiesSubEntityID")->default(0);
            $table->integer("UnitTypesUnitTypeID")->default(0);
            $table->integer("AccountsAccountType")->default(0);
            $table->integer("SubEntitiesSortOrder")->default(0);
            $table->date("InvoiceDate")->nullable();
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
        Schema::dropIfExists('report_invoices');
    }
};
