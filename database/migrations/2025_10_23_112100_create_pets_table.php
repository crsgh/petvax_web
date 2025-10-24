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
        Schema::create('pets', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('species');
            $table->string('breed')->nullable();
            $table->date('birth_date');
            $table->enum('gender', ['male', 'female']);
            $table->decimal('weight', 5, 2)->nullable();
            $table->string('size', 10)->nullable();
            $table->string('image')->nullable();
            $table->foreignId('owner_id')->constrained('users')->onDelete('cascade');
            $table->integer('clinic_id')->nullable();
            $table->timestamps();
            $table->timestamp('deleted_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pets');
    }
};
