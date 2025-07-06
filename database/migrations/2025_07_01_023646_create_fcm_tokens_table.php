<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('fcm_tokens', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id'); // Untuk mengaitkan token dengan pengguna
            $table->string('fcm_token', 255)->unique(); // Token FCM unik untuk setiap perangkat
            $table->timestamps();

            $table->foreign('user_id')
                ->references('id_user') // Mengacu pada primary key tabel users Anda
                ->on('users')
                ->onDelete('cascade'); // Hapus token jika pengguna dihapus
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fcm_tokens');
    }
};
