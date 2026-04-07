<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('produtos', function (Blueprint $table) {
            $table->id(); // Gera o id_produto
            $table->string('nome');
            $table->string('principio_ativo');
            $table->string('dosagem');
            $table->decimal('preco_unitario', 8, 2); // 8 dígitos totais, 2 após a vírgula
            $table->timestamps(); // Cria as colunas created_at e updated_at automaticamente
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produtos');
    }
};
