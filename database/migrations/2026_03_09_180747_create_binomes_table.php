<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration{
    public function up(): void{
        Schema::create('binomes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_binome1')->constrained('users')->cascadeOnDelete();
            $table->foreignId('id_binome2')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('id_mentor')->nullable()->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('binomes');
    }
};