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
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('polling_options');
    }
};
