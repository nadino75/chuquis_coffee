<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reportes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->enum('tipo', ['ventas', 'pagos', 'productos', 'inventario', 'clientes', 'general']);
            $table->json('filtros')->nullable(); // Para almacenar los filtros aplicados
            $table->json('datos')->nullable(); // Para almacenar los datos del reporte
            $table->json('configuracion')->nullable(); // Configuración de gráficos
            $table->unsignedBigInteger('usuario_id');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('usuario_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reportes');
    }
};