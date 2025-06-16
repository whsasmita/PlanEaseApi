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
        Schema::create('pollings', function (Blueprint $table) {
            $table->increments('id_polling');

            $table->unsignedInteger('user_id');
            $table->foreign('user_id')
                ->references('id_user')
                ->on('users')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->string('title', 100);
            $table->text('description')->nullable();
            $table->text('polling_image')->nullable();
            $table->datetime('deadline'); // Changed from date to datetime
            $table->timestamps();
        });

        Schema::create('polling_options', function (Blueprint $table) {
            $table->increments('id_option');

            $table->unsignedInteger('polling_id');
            $table->foreign('polling_id')
                ->references('id_polling')
                ->on('pollings')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->string('option', 255);
            $table->timestamps();
        });

        Schema::create('polling_votes', function (Blueprint $table) {
            $table->increments('id_vote');

            $table->unsignedInteger('polling_id');
            $table->foreign('polling_id')
                ->references('id_polling')
                ->on('pollings')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->unsignedInteger('polling_option_id');
            $table->foreign('polling_option_id')
                ->references('id_option')
                ->on('polling_options')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->unsignedInteger('user_id')->nullable(); 
            $table->foreign('user_id')->references('id_user')->on('users');

            $table->unique(['polling_id', 'user_id']); 

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pollings');
        Schema::dropIfExists('polling_options');
        Schema::dropIfExists('polling_votes');
    }
};