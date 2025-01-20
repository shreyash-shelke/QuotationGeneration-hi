<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('quotation_items', function (Blueprint $table) {
        $table->id();
        $table->foreignId('quotation_id')->constrained('quotations')->onDelete('cascade');
        $table->string('name');
        $table->text('description');
        $table->integer('quantity');
        $table->decimal('price', 8, 2);
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotation_items');
    }
};
