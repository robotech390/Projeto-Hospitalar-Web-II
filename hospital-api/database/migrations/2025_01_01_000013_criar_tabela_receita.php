<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        if (Schema::hasTable('receita')) return;
        Schema::create('receita', function (Blueprint $table) {
            $table->id();
            $table->text('observacoes')->nullable();
            $table->string('farmacia', 100)->nullable();
            $table->date('data_emissao')->nullable();
            $table->unsignedBigInteger('id_consulta')->nullable();
            $table->datetime('data_criacao')->useCurrent();
            $table->datetime('data_alteracao')->nullable()->useCurrentOnUpdate();
        });
    }
    public function down(): void { Schema::dropIfExists('receita'); }
};
