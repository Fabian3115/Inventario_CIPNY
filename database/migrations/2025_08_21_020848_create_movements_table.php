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
        Schema::create('movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->date('date_products'); // Fecha que se realizó el movimiento
            $table->enum('type', ['entrada', 'salida']);
            $table->bigInteger('amount');
            $table->string('delivered_to')->nullable(); // Persona a quien se entrega el producto
            $table->string('area')->nullable(); // Área que recibe el producto
            $table->string('taken_by')->nullable(); // Persona que toma el producto
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movements');
    }
};
