<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        if (Schema::hasTable('itens_exame')) return;
        Schema::create('itens_exame', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_solicitacao')->nullable();
            $table->unsignedBigInteger('id_tipo_exame')->nullable();
            $table->string('status', 30)->default('Pendente')->comment('Pendente | Coletado | Em Análise | Concluído');
            $table->text('laudo')->nullable();
            $table->string('arquivo', 255)->nullable()->comment('Caminho do arquivo PDF/imagem do resultado');
            $table->date('data_resultado')->nullable();
            $table->datetime('data_criacao')->useCurrent();
            $table->datetime('data_alteracao')->nullable()->useCurrentOnUpdate();
        });
    }
    public function down(): void { Schema::dropIfExists('itens_exame'); }
};
