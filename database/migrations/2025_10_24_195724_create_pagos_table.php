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
            $table->string('cliente_ci', 12);
            $table->unsignedBigInteger('pago_mixto_id')->nullable();

            $table->foreign('cliente_ci')->references('ci')->on('clientes')->onDelete('cascade');
            $table->foreign('pago_mixto_id')
                ->references('id')
                ->on('pagos')
                ->onDelete('set null');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pagos');
    }
};