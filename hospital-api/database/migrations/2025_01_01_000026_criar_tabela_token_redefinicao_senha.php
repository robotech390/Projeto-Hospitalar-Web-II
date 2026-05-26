<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tabela que armazena os tokens enviados por e-mail para
 * redefinição de senha (esqueci minha senha).
 *
 * O token expira em 60 minutos a partir da criação.
 */
return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('token_redefinicao_senha')) return;

        Schema::create('token_redefinicao_senha', function (Blueprint $table) {
            $table->id();
            $table->string('email', 345)->index();
            $table->string('token', 64)->unique();
            $table->datetime('expira_em');
            $table->datetime('data_criacao')->useCurrent();
            $table->datetime('utilizado_em')->nullable()->comment('Quando o token foi utilizado (null = ainda válido)');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('token_redefinicao_senha');
    }
};
