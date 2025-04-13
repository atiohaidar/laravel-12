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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->constrained('event_categories');
            $table->string('title');
            $table->text('description');
            $table->dateTime('start_time');
            $table->dateTime('end_time')->nullable();
            $table->string('location');
            $table->text('location_details')->nullable();
            $table->string('banner_image')->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->boolean('is_paid')->default(false);
            $table->integer('capacity')->nullable();
            $table->enum('status', ['upcoming', 'ongoing', 'completed', 'cancelled'])->default('upcoming');
            $table->boolean('is_published')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
