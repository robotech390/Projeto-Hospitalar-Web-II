<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        if (Schema::hasTable('usuario')) return;
        Schema::create('usuario', function (Blueprint $table) {
            $table->id();
            $table->string('usuario', 100);
            $table->string('email', 345)->unique();
            $table->string('senha', 255)->comment('Hash bcrypt');
            $table->string('funcao', 50)->comment('administrador|medico|farmaceutico|recepcionista|paciente');
            $table->unsignedBigInteger('id_pessoa')->nullable();
            $table->unsignedBigInteger('id_cadastro')->nullable();
            $table->boolean('primeiro_acesso')->default(true);
            $table->datetime('data_criacao')->useCurrent();
            $table->datetime('data_alteracao')->nullable()->useCurrentOnUpdate();
        });
    }
    public function down(): void { Schema::dropIfExists('usuario'); }
};
