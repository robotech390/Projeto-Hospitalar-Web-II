<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        if (Schema::hasTable('agenda')) return;
        Schema::create('agenda', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_medico');
            $table->date('data_disponibilidade');
            $table->time('hora_inicio');
            $table->time('hora_fim');
            $table->boolean('plantao')->default(false)->comment('false = turno normal | true = plantão');
            $table->datetime('data_criacao')->useCurrent();
            $table->datetime('data_alteracao')->nullable()->useCurrentOnUpdate();
        });
    }
    public function down(): void { Schema::dropIfExists('agenda'); }
};
