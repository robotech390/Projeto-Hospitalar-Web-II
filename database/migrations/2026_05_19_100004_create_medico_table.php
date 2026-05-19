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
        Schema::create('medico', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_pessoa')
                  ->nullable()
                  ->references('id')
                  ->on('pessoa')
                  ->nullOnDelete();
            $table->string('especialidade', 100)->nullable();
            $table->string('sub_especialidade', 100)->nullable();
            $table->string('crm', 20)->nullable();
            $table->char('uf_crm', 2)->nullable();
            $table->enum('tipo', ['Geral', 'Especialista'])->nullable();
            $table->string('status', 20)->default('ativo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medico');
    }
};
