<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        if (Schema::hasTable('lote_notafiscal')) return;
        Schema::create('lote_notafiscal', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_nota_fiscal')->nullable();
            $table->decimal('icms', 5, 2)->nullable()->comment('Percentual ICMS');
            $table->char('cfop', 4)->nullable()->comment('Código Fiscal de Operações');
            $table->integer('quantidade')->default(0);
            $table->unsignedBigInteger('id_lote')->nullable();
            $table->datetime('data_criacao')->useCurrent();
            $table->datetime('data_alteracao')->nullable()->useCurrentOnUpdate();
        });
    }
    public function down(): void { Schema::dropIfExists('lote_notafiscal'); }
};
