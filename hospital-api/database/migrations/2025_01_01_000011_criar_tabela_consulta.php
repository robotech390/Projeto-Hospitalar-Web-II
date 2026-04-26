<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        if (Schema::hasTable('consulta')) return;
        Schema::create('consulta', function (Blueprint $table) {
            $table->id();
            $table->text('descricao')->nullable();
            $table->date('data')->nullable();
            $table->datetime('hora_inicio')->nullable();
            $table->datetime('hora_fim')->nullable();
            $table->datetime('data_check_in')->nullable();
            $table->string('status', 50)->nullable()->comment('Ex: Agendada, Em andamento, Concluída, Cancelada');
            $table->unsignedBigInteger('id_tipo_consulta')->nullable();
            $table->unsignedBigInteger('id_paciente')->nullable();
            $table->unsignedBigInteger('id_medico')->nullable();
            $table->datetime('data_criacao')->useCurrent();
            $table->datetime('data_alteracao')->nullable()->useCurrentOnUpdate();
        });
    }
    public function down(): void { Schema::dropIfExists('consulta'); }
};
