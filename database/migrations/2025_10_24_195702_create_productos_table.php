<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100);
            $table->text('descripcion')->nullable(); // COLUMNA FALTANTE
            $table->integer('stock')->default(0);
            $table->integer('stock_minimo')->default(5); // COLUMNA FALTANTE
            $table->decimal('precio', 8, 2)->default(0);
            $table->unsignedBigInteger('categoria_id');
            $table->string('imagen')->nullable(); // COLUMNA FALTANTE

            $table->foreign('categoria_id')->references('id')->on('categorias')->onDelete('cascade');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};