<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->integer('code')->unique();
            $table->text('description');
            $table->integer('stock');
            $table->string('categories');
            $table->string('extent'); //medida
            $table->string('warehouse')->nullable(); //almacén
            $table->date('date_products'); //Fecha que se añadió el producto al Stock
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
