<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        if (Schema::hasTable('usuario_log')) return;
        Schema::create('usuario_log', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_usuario')->nullable();
            $table->longText('log');
            $table->datetime('data')->useCurrent()->comment('Momento exato da ação — imutável');
        });
        // Sem data_criacao/data_alteracao: logs são imutáveis por natureza
    }
    public function down(): void { Schema::dropIfExists('usuario_log'); }
};
