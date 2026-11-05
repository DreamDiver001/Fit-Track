<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('workout_exercises', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workout_id')->constrained()->cascadeOnDelete();
            $table->foreignId('exercise_id')->constrained()->cascadeOnDelete();
            $table->integer('sets')->nullable();
            $table->integer('reps')->nullable();
            $table->integer('rest_seconds')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->unique(['workout_id', 'exercise_id']);
        });
    }
    public function down(): void { Schema::dropIfExists('workout_exercises'); }
};
