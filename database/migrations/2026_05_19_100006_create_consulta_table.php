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
        Schema::create('consulta', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_paciente')
                  ->references('id')
                  ->on('usuario')
                  ->cascadeOnDelete();
            $table->foreignId('id_medico')
                  ->references('id')
                  ->on('medico')
                  ->cascadeOnDelete();
            $table->foreignId('id_tipo_consulta')
                  ->references('id')
                  ->on('tipo_consulta')
                  ->cascadeOnDelete();
            $table->date('data');
            $table->time('hora_inicio');
            $table->time('hora_fim')->nullable();
            $table->string('status', 50)->default('agendado');
            $table->string('descricao', 255)->nullable();
            $table->dateTime('data_check_in')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consulta');
    }
};
