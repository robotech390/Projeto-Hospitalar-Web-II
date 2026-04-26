<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        if (Schema::hasTable('tipo_exame')) return;
        Schema::create('tipo_exame', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 100);
            $table->string('tipo', 50)->nullable()->comment('Ex: Sangue, Raio-X, Imagem');
            $table->decimal('preco', 10, 2)->nullable();
            $table->text('preparo')->nullable()->comment('Instruções de preparo para o paciente');
            $table->datetime('data_criacao')->useCurrent();
            $table->datetime('data_alteracao')->nullable()->useCurrentOnUpdate();
        });
    }
    public function down(): void { Schema::dropIfExists('tipo_exame'); }
};
