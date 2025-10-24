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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->default(5)->constrained('roles')->onDelete('cascade');
            $table->string('avatar', 100)->nullable();
            $table->string('name');
            $table->string('contact_number', 15)->nullable();
            $table->string('email')->unique();
            $table->string('address', 200)->nullable();
            $table->string('password');
            $table->foreignId('clinic_id')->constrained('clinics')->onDelete('cascade');
            $table->boolean('is_verified')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
