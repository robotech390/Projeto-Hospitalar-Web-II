<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        if (Schema::hasTable('medico')) return;
        Schema::create('medico', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_pessoa')->nullable();
            $table->string('tipo', 50)->nullable()->comment('Ex: Clínico Geral, Especialista');
            $table->string('crm', 6);
            $table->char('uf_crm', 2)->default('SC');
            $table->string('especialidade', 100)->nullable();
            $table->string('sub_especialidade', 100)->nullable();
            $table->date('data_contratacao')->nullable();
            $table->char('status', 1)->default('A')->comment('A = Ativo | I = Inativo');
            $table->datetime('data_criacao')->useCurrent();
            $table->datetime('data_alteracao')->nullable()->useCurrentOnUpdate();
        });
    }
    public function down(): void { Schema::dropIfExists('medico'); }
};
