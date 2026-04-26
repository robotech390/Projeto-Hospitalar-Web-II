<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        if (Schema::hasTable('solicitacao_exame')) return;
        Schema::create('solicitacao_exame', function (Blueprint $table) {
            $table->id();
            $table->datetime('data')->nullable();
            $table->text('justificativa')->nullable();
            $table->integer('prioridade')->default(1)->comment('1 = Normal | 2 = Urgente | 3 = Emergência');
            $table->unsignedBigInteger('id_consulta')->nullable();
            $table->datetime('data_criacao')->useCurrent();
            $table->datetime('data_alteracao')->nullable()->useCurrentOnUpdate();
        });
    }
    public function down(): void { Schema::dropIfExists('solicitacao_exame'); }
};
