<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        if (Schema::hasTable('tipo_medicamento')) return;
        Schema::create('tipo_medicamento', function (Blueprint $table) {
            $table->id();
            $table->string('descricao', 100);
            $table->datetime('data_criacao')->useCurrent();
            $table->datetime('data_alteracao')->nullable()->useCurrentOnUpdate();
        });
    }
    public function down(): void { Schema::dropIfExists('tipo_medicamento'); }
};
