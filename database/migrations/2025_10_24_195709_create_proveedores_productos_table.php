<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	public function up(): void
	{
		Schema::create('proveedores_productos', function (Blueprint $table) {
			$table->id();
			$table->unsignedBigInteger('proveedore_id');
			$table->unsignedBigInteger('producto_id');
			$table->decimal('cantidad', 3, 2)->default(0);
			$table->date('fecha_compra');
			$table->date('fecha_vencimiento');
			$table->unsignedBigInteger('marca_id');

			$table->foreign('proveedore_id')->references('id')->on('proveedores')->onDelete('cascade');
			$table->foreign('producto_id')->references('id')->on('productos')->onDelete('cascade');
			$table->foreign('marca_id')->references('id')->on('marcas')->onDelete('cascade');

			$table->timestamps();
		});
	}

	public function down(): void
	{
		Schema::dropIfExists('proveedores_productos');
	}
};
