<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        if (Schema::hasTable('pessoa')) return;
        Schema::create('pessoa', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 100);
            $table->string('cpf', 11)->unique()->nullable();
            $table->date('data_nascimento')->nullable();
            $table->string('email', 100)->unique()->nullable();
            $table->string('telefone', 11)->nullable();
            $table->unsignedBigInteger('id_endereco')->nullable();
            $table->datetime('data_criacao')->useCurrent();
            $table->datetime('data_alteracao')->nullable()->useCurrentOnUpdate();
        });
    }
    public function down(): void { Schema::dropIfExists('pessoa'); }
};
