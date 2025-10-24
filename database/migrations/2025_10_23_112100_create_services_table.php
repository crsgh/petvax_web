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
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('image', 100)->nullable();
            $table->string('category', 50)->default('grooming');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->integer('duration')->nullable();
            $table->foreignId('species')->nullable()->constrained('species')->onDelete('set null');
            $table->enum('size', ['small', 'medium', 'large'])->default('medium');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->foreignId('clinic_id')->constrained('clinics')->onDelete('cascade');
            $table->boolean('home_service')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
