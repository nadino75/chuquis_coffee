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
        Schema::create('reportes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->text('descripcion')->nullable(); // Cambiado a text y nullable
            $table->enum('tipo', ['ventas', 'pagos', 'productos', 'inventario', 'clientes']); // Especificar tipos
            $table->enum('estado', ['pendiente', 'procesando', 'completado', 'error'])->default('pendiente');
            $table->unsignedBigInteger('usuario_id');
            $table->unsignedBigInteger('venta_id')->nullable();
            $table->unsignedBigInteger('pago_id')->nullable();
            $table->unsignedBigInteger('producto_id')->nullable();
            $table->json('parametros')->nullable(); // Para almacenar filtros y parámetros del reporte
            $table->string('archivo_ruta')->nullable(); // Ruta del archivo generado
            $table->dateTime('fecha_reporte');
            $table->dateTime('fecha_completado')->nullable(); // Cuando se complete el reporte
            $table->timestamps();

            // Claves foráneas - asumiendo que la tabla de usuarios es 'users'
            $table->foreign('usuario_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('venta_id')->references('id')->on('ventas')->onDelete('set null');
            $table->foreign('pago_id')->references('id')->on('pagos')->onDelete('set null');
            $table->foreign('producto_id')->references('id')->on('productos')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reportes');
    }
};
