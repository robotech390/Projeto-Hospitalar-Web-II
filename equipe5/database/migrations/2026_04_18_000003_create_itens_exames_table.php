<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('itens_exame', function (Blueprint $blueprint) {
            $blueprint->id();
            $blueprint->foreignId('id_solicitacao')->constrained('solicitacao_exame');
            $blueprint->foreignId('id_tipo_exame')->constrained('tipo_exame');
            $blueprint->string('status')->default('Pendente');
            $blueprint->text('laudo')->nullable();
            $blueprint->string('arquivo')->nullable(); // Can be an enum in the model
            $blueprint->timestamp('data_resultado')->nullable();
            $blueprint->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('itens_exame');
    }
};
