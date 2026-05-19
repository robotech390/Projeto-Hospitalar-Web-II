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
        Schema::create('pessoa', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 255);
            $table->string('cpf', 14)->nullable();
            $table->date('data_nascimento')->nullable();
            $table->string('email', 100)->nullable();
            $table->string('telefone', 20)->nullable();
            $table->foreignId('id_endereco')
                  ->nullable()
                  ->references('id')
                  ->on('endereco')
                  ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pessoa');
    }
};
