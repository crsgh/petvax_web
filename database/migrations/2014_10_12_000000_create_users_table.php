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
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();
            $table->timestamps();
        });
        
        Schema::create('clinics', function (Blueprint $table) {
            $table->string('name');
            $table->string('contact');
            $table->string('email')->unique();
            $table->string('address');
            $table->double('latitute');
            $table->double('longitude');
            $table->string('image')->nullable();
            $table->string('status')->default('active');
            $table->string('opening_time');
            $table->string('closing_time');
            $table->string('description')->nullable();
            $table->timestamps();
        });

        Schema::create('users', function (Blueprint $table) {
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('role_id')->default('0');
            $table->string('clinic_id')->nullable();
            $table->index('role_id');
            $table->index('clinic_id');
            $table->timestamps();
        });

        Schema::create('pets', function (Blueprint $table) {
            $table->string('name');
            $table->string('species');
            $table->string('breed');
            $table->string('birth_date');
            $table->string('gender');
            $table->double('weight')->nullable();
            $table->string('image')->nullable();
            $table->string('owner_id');
            $table->index('owner_id');
            $table->timestamps();
        });

        Schema::create('medical_histories', function (Blueprint $table) {
            $table->string('pet_id');
            $table->string('clinic_id');
            $table->string('visit_date');
            $table->string('diagnosis');
            $table->string('treatment');
            $table->string('notes')->nullable();
            $table->double('temperature')->nullable();
            $table->string('attending_vet');
            $table->double('weight')->nullable();
            $table->index('pet_id');
            $table->index('clinic_id');
            $table->timestamps();
        });

        Schema::create('services', function (Blueprint $table) {
            $table->string('name');
            $table->string('description')->nullable();
            $table->double('price');
            $table->string('species')->default('feline');
            $table->string('size')->default('medium');
            $table->string('status')->default('active');
            $table->string('clinic_id');
            $table->index('clinic_id');
            $table->timestamps();
        });

        Schema::create('bookings', function (Blueprint $table) {
            $table->string('pet_id');
            $table->string('service_id');
            $table->string('clinic_id');
            $table->string('appointment_datetime');
            $table->string('status')->default('pending');
            $table->string('notes')->nullable();
            $table->double('total_amount');
            $table->index('pet_id');
            $table->index('service_id');
            $table->index('clinic_id');
            $table->timestamps();
        });

        Schema::create('inventory_items', function (Blueprint $table) {
            $table->string('name');
            $table->string('category');
            $table->string('description')->nullable();
            $table->string('sku')->nullable();
            $table->double('unit_price')->default(0);
            $table->integer('quantity');
            $table->string('added_by')->nullable();
            $table->string('clinic_id');
            $table->index('added_by');
            $table->index('clinic_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
        Schema::dropIfExists('clinics');
        Schema::dropIfExists('users');
        Schema::dropIfExists('pets');
        Schema::dropIfExists('medical_histories');
        Schema::dropIfExists('services');
        Schema::dropIfExists('bookings');
        Schema::dropIfExists('inventory_items');
    }
};
