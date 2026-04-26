<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        if (Schema::hasTable('medicamento')) return;
        Schema::create('medicamento', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 100);
            $table->string('dosagem', 50)->nullable();
            $table->string('principio_ativo', 100)->nullable();
            $table->unsignedBigInteger('id_tipo_medicamento')->nullable();
            $table->decimal('preco', 10, 2)->nullable();
            $table->datetime('data_criacao')->useCurrent();
            $table->datetime('data_alteracao')->nullable()->useCurrentOnUpdate();
        });
    }
    public function down(): void { Schema::dropIfExists('medicamento'); }
};
