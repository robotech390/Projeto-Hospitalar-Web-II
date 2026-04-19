<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tipo_exame', function (Blueprint $blueprint) {
            $blueprint->id();
            $blueprint->string('nome');
            $blueprint->string('tipo');
            $blueprint->decimal('preco', 10, 2);
            $blueprint->text('preparo')->nullable();
            $blueprint->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tipo_exame');
    }
};
