<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        if (Schema::hasTable('medicamento_receita')) return;
        Schema::create('medicamento_receita', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_receita')->nullable();
            $table->unsignedBigInteger('id_medicamento')->nullable();
            $table->integer('quantidade')->default(1);
            $table->string('posologia', 200)->nullable()->comment('Ex: 1 comprimido de 8 em 8 horas');
            $table->datetime('data_criacao')->useCurrent();
            $table->datetime('data_alteracao')->nullable()->useCurrentOnUpdate();
        });
    }
    public function down(): void { Schema::dropIfExists('medicamento_receita'); }
};
