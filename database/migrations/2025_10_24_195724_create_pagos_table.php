<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pagos', function (Blueprint $table) {
            $table->id();
            $table->string('recibo', 25);
            $table->date('fecha');
            $table->enum('tipo_pago', ['efectivo', 'qr', 'mixto', 'tarjeta', 'transferencia']);
            $table->decimal('total_pagado', 8, 2)->default(0);
            $table->char('cliente_ci', 12);

            $table->foreign('cliente_ci')->references('ci')->on('clientes')->onDelete('cascade');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pagos');
    }
};