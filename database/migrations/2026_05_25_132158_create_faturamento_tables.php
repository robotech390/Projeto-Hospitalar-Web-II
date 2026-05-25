<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tipo_cobranca', function (Blueprint $table) {
            $table->id();
            $table->string('descricao');
            $table->timestamps();
        });

        Schema::create('convenio', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('cnpj')->nullable();
            $table->string('telefone')->nullable();
            $table->string('email')->nullable();
            $table->timestamps();
        });

        Schema::create('plano', function (Blueprint $table) {
            $table->id();
            $table->string('descricao');
            $table->foreignId('id_tipo_cobranca')
                ->nullable()
                ->constrained('tipo_cobranca')
                ->nullOnDelete();

            $table->foreignId('id_convenio')
                ->nullable()
                ->constrained('convenio')
                ->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plano');
        Schema::dropIfExists('convenio');
        Schema::dropIfExists('tipo_cobranca');
    }
};