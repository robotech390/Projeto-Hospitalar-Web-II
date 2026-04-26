<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        if (Schema::hasTable('nota_fiscal')) return;
        Schema::create('nota_fiscal', function (Blueprint $table) {
            $table->id();
            $table->string('numero', 50)->nullable();
            $table->string('cpf_cnpj', 18)->nullable();
            $table->unsignedBigInteger('destinatario')->nullable();
            $table->datetime('data')->nullable();
            $table->char('tipo', 1)->nullable()->comment('E = Entrada | S = Saída');
            $table->datetime('data_criacao')->useCurrent();
            $table->datetime('data_alteracao')->nullable()->useCurrentOnUpdate();
        });
    }
    public function down(): void { Schema::dropIfExists('nota_fiscal'); }
};
