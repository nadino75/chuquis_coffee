<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pagos_mixtos', function (Blueprint $table) {
            $table->id();
            $table->string('tipo_pago', 100);
            $table->decimal('monto', 8, 2)->default(0);
            $table->unsignedBigInteger('pago_id');
            $table->timestamps();

            $table->foreign('pago_id')
                  ->references('id')
                  ->on('pagos')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pagos_mixtos');
    }
};