<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('workout_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('workout_id')->constrained()->cascadeOnDelete();
            $table->date('logged_at');
            $table->integer('duration_minutes')->nullable();
            $table->integer('calories_burned')->nullable();
            $table->enum('effort_rating', ['great','okay','tough'])->default('okay');
            $table->text('notes')->nullable();
            $table->string('tags')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('workout_logs'); }
};
