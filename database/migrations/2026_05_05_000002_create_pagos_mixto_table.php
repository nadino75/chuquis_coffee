<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pagos_mixto', function (Blueprint $table) {
            $table->id();
            $table->string('recibo', 50)->nullable();
            $table->date('fecha')->nullable();
            $table->enum('tipo_pago', ['efectivo', 'tarjeta', 'transferencia', 'qr'])->nullable();
            $table->decimal('monto', 10, 2)->default(0);
            $table->unsignedBigInteger('pago_id')->nullable();
            $table->foreign('pago_id')->references('id')->on('pagos')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pagos_mixto');
    }
};
