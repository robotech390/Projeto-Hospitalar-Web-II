<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        if (Schema::hasTable('plano_cobertura_consulta')) return;
        Schema::create('plano_cobertura_consulta', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_plano')->nullable();
            $table->unsignedBigInteger('id_tipo_consulta')->nullable();
            $table->datetime('data_criacao')->useCurrent();
            $table->datetime('data_alteracao')->nullable()->useCurrentOnUpdate();
        });
    }
    public function down(): void { Schema::dropIfExists('plano_cobertura_consulta'); }
};
