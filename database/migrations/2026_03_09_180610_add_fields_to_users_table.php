<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration{
    public function up(): void{
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'stagiaire', 'mentor'])->default('stagiaire');
            $table->foreignId('id_user_invite')->nullable()->constrained('users')->nullOnDelete();
            $table->string('invite_token')->nullable();
            $table->boolean('accepted')->default(false);
            $table->timestamp('accepted_at')->nullable();
        });
    }

    public function down(): void {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'accepted', 'accepted_at']);
        });
    }
};
