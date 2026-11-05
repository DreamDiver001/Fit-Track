<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('workouts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('category', ['cardio','strength','flexibility','hiit','core','full_body'])->default('strength');
            $table->enum('difficulty', ['easy','medium','hard'])->default('medium');
            $table->integer('estimated_duration')->nullable();
            $table->integer('estimated_calories')->nullable();
            $table->string('image_path')->nullable();
            $table->boolean('is_public')->default(true);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('workouts'); }
};
