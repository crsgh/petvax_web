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
        Schema::create('rule_bases', function (Blueprint $table) {
            $table->id();
            $table->enum('target', ['dog', 'cat']);
            $table->string('question', 300);
            $table->string('yes', 200);
            $table->string('no', 200);
            $table->boolean('is_first_question')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rule_bases');
    }
};
