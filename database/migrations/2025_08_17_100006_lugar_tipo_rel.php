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
        Schema::create('lugar_tipo_rel', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lugar_id')->constrained('lugares')->onDelete('cascade');
            $table->foreignId('tipo_lugar_id')->constrained('tipo_lugares')->onDelete('cascade');
             $table->unique(['lugar_id', 'tipo_lugar_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lugar_tipo_rel');
    }
};
