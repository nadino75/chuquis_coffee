<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('categorias', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100)->unique();
            $table->text('descripcion')->nullable();
            $table->unsignedBigInteger('tipo_id');
            $table->unsignedBigInteger('categoria_id')->nullable();

            $table->foreign('tipo_id')->references('id')->on('tipos')->onDelete('cascade');
            $table->foreign('categoria_id')->references('id')->on('categorias')->onDelete('set null');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categorias');
    }
};
