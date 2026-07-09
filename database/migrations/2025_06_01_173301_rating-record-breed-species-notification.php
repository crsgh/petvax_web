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
        Schema::create('species', function (Blueprint $table) {
            $table->string('name');
            $table->string('clinic_id');
            $table->index('clinic_id');
            $table->timestamps();
        });

        Schema::create('breeds', function (Blueprint $table) {
            $table->string('name');
            $table->string('species_id');
            $table->string('clinic_id');
            $table->index('species_id');
            $table->index('clinic_id');
            $table->timestamps();
        });

        Schema::create('clinic_ratings', function (Blueprint $table) {
            $table->string('user_id');
            $table->string('clinic_id');
            $table->integer('rating');
            $table->string('comment')->nullable();
            $table->index('user_id');
            $table->index('clinic_id');
            $table->timestamps();
        });

        Schema::create('notifications', function (Blueprint $table) {
            $table->string('user_id');
            $table->string('title');
            $table->string('message');
            $table->string('type');
            $table->boolean('is_read')->default(false);
            $table->index('user_id');
            $table->timestamps();
        });

        Schema::create('activity_records', function (Blueprint $table) {
            $table->string('user_id');
            $table->string('clinic_id');
            $table->string('action');
            $table->string('description');
            $table->index('user_id');
            $table->index('clinic_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_records');
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('clinic_ratings');
        Schema::dropIfExists('breeds');
        Schema::dropIfExists('species');
    }
};
