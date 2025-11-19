<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('clientes', function (Blueprint $table) {
            $table->char('ci', 12)->primary();
            $table->char('NIT', 13)->nullable();
            $table->string('nombres', 100);
            $table->string('apellido_paterno', 100)->nullable();
            $table->string('apellido_materno', 100)->nullable();
            $table->enum('sexo', ['masculino', 'femenino'])->nullable();
            $table->char('telefono', 10)->nullable();
            $table->char('celular', 10);
            $table->string('correo', 100)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
