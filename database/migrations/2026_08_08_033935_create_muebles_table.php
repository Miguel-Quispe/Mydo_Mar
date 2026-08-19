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
    Schema::create('muebles', function (Blueprint $table) {
        $table->id();
        
        // Relación con el maestro carpintero
        $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
        
        $table->string('nombre');
        $table->text('descripcion')->nullable();
        $table->text('parametros_diseno')->nullable(); // Dimensiones, maderas, etc.
        
        // Visibilidad (true = Publicado, false = Privado/Borrador)
        $table->boolean('es_publico')->default(false); 
        
        // Contador de visitas
        $table->unsignedBigInteger('vistas')->default(0); 

        $table->timestamps();
        $table->softDeletes();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('muebles');
    }
};
