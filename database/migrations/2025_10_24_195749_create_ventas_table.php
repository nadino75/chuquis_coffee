<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ventas', function (Blueprint $table) {
            $table->id();
            $table->date('fecha_venta');
            $table->decimal('suma_total', 5, 2)->default(0);
            $table->unsignedBigInteger('cliente_id');
            $table->unsignedBigInteger('pago_id');
            $table->string('detalles')->nullable();

            $table->foreign('cliente_id')
                ->references('id')
                ->on('clientes')
                ->onDelete('cascade');

            $table->foreign('pago_id')
                ->references('id')
                ->on('pagos')
                ->onDelete('cascade');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ventas');
    }
};