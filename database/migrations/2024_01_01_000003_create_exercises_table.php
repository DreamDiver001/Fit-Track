<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('exercises', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('muscle_group', ['chest','back','legs','core','arms','shoulders','full_body'])->default('full_body');
            $table->enum('equipment', ['barbell','dumbbell','bodyweight','machine','cable','other'])->default('bodyweight');
            $table->text('instructions')->nullable();
            $table->string('image_path')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('exercises'); }
};
