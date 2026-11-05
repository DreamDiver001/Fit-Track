<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('goals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->enum('goal_type', ['weight_loss','endurance','strength','habit','custom'])->default('custom');
            $table->decimal('target_value', 8, 2)->nullable();
            $table->decimal('current_value', 8, 2)->nullable();
            $table->string('unit')->nullable();
            $table->date('deadline')->nullable();
            $table->enum('status', ['in_progress','completed','abandoned'])->default('in_progress');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('goals'); }
};
