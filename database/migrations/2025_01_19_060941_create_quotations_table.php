<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuotationsTable extends Migration
{
    public function up()
    {
        Schema::create('quotations', function (Blueprint $table) {
            $table->id();
            $table->string('quotation_number')->unique();  // Quotation number
            $table->string('customer_name');  // Customer name
            $table->decimal('total_amount', 10, 2);  // Total amount without GST
            $table->decimal('gst_amount', 10, 2);  // GST amount
            $table->decimal('total_with_gst', 10, 2);  // Total amount including GST
            $table->date('due_date');  // Due date for the quotation
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('quotations');
    }
}
