<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuotationItemsTable extends Migration
{
    public function up()
    {
        Schema::create('quotation_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quotation_id')->constrained('quotations');  // Foreign key to the quotations table
            $table->string('description');  // Item description
            $table->integer('quantity');  // Quantity
            $table->decimal('amount', 10, 2);  // Item amount
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('quotation_items');
    }
}
