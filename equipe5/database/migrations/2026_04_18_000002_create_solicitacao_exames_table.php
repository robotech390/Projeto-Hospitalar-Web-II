<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('solicitacao_exame', function (Blueprint $blueprint) {
            $blueprint->id();
            $blueprint->timestamp('data');
            $blueprint->text('justificativa')->nullable();
            $blueprint->integer('prioridade')->default(1);
            $blueprint->unsignedBigInteger('id_consulta');
            $blueprint->string('status')->default('Pendente');
            $blueprint->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('solicitacao_exame');
    }
};
