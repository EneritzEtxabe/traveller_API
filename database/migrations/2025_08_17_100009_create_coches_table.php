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
        Schema::create('coches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('marca_id')->constrained('marca_coches')->onDelete('cascade');
            $table->foreignId('carroceria_id')->constrained('carroceria_coches')->onDelete('cascade');
            $table->year('ano')->nullable();
            $table->integer('nPlazas');
            $table->enum('cambio', ['manual', 'automatico'])->nullable();
            $table->enum('estado', ['disponible', 'mantenimiento'])->default('disponible');
            $table->decimal('costeDia', 4, 2);
            $table->foreignId('pais_id')->constrained('paises')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coches');
    }
};
