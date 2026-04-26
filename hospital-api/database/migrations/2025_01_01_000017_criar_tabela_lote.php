<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        if (Schema::hasTable('lote')) return;
        Schema::create('lote', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_medicamento')->nullable();
            $table->integer('numero')->nullable();
            $table->date('data_validade')->nullable();
            $table->integer('quantidade_produtos')->default(0);
            $table->boolean('ativo')->default(true);
            $table->datetime('data_criacao')->useCurrent();
            $table->datetime('data_alteracao')->nullable()->useCurrentOnUpdate();
        });
    }
    public function down(): void { Schema::dropIfExists('lote'); }
};
