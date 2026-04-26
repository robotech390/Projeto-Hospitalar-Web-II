<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        if (Schema::hasTable('diagnostico')) return;
        Schema::create('diagnostico', function (Blueprint $table) {
            $table->id();
            $table->string('cid', 10)->nullable()->comment('Código CID-10');
            $table->text('descricao')->nullable();
            $table->unsignedBigInteger('id_consulta')->nullable();
            $table->datetime('data_criacao')->useCurrent();
            $table->datetime('data_alteracao')->nullable()->useCurrentOnUpdate();
        });
    }
    public function down(): void { Schema::dropIfExists('diagnostico'); }
};
