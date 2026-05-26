<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recuperacao_senha', function (Blueprint $table) {
            $table->id();
            $table->string('email')->index();
            $table->string('codigo');
            $table->timestamp('expira_em');
            $table->timestamp('created_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recuperacao_senha');
    }
};