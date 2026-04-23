<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('venta_productos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_producto');
            $table->unsignedBigInteger('id_venta');
            $table->decimal('precio', 3, 2)->default(0);
            $table->integer('cantidad')->default(1);
            $table->timestamps();

            $table->foreign('id_producto')
                ->references('id')
                ->on('productos')
                ->onDelete('cascade');

            $table->foreign('id_venta')
                ->references('id')
                ->on('ventas')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('venta_productos');
    }
};