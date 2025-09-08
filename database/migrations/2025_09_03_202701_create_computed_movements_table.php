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
        Schema::create('computed_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('computation_id')->constrained()->onDelete('cascade');
            $table->date('movement_date');
            $table->enum('type', ['salida']);
            $table->bigInteger('amount');
            $table->string('delivered_to')->nullable(); // Persona a quien se entrega el computo
            $table->string('area')->nullable(); // Área que recibe el computo
            $table->string('taken_by')->nullable(); // Persona que toma el computo
            $table->string('seat')->nullable(); // Sede que toma el computo

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('computed_movements');
    }
};
