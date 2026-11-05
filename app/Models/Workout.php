<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Workout extends Model {
    use HasFactory;
    protected $fillable = ['title','description','category','difficulty','estimated_duration','estimated_calories','image_path','is_public'];
    protected $casts    = ['is_public'=>'boolean'];

    public function exercises()   { return $this->belongsToMany(Exercise::class,'workout_exercises')->withPivot('sets','reps','rest_seconds','sort_order')->orderByPivot('sort_order'); }
    public function workoutLogs() { return $this->hasMany(WorkoutLog::class); }

    public function getCategoryLabelAttribute() {
        return ['cardio'=>'Cardio','strength'=>'Strength','flexibility'=>'Flexibility','hiit'=>'HIIT','core'=>'Core','full_body'=>'Full Body'][$this->category] ?? ucfirst($this->category);
    }
    public function getDifficultyColorAttribute() {
        return ['easy'=>'badge-success','medium'=>'badge-warning','hard'=>'badge-error'][$this->difficulty] ?? 'badge-ghost';
    }
    public function getCategoryColorAttribute() {
        return ['cardio'=>'badge-success','strength'=>'badge-info','flexibility'=>'badge-secondary','hiit'=>'badge-warning','core'=>'badge-accent','full_body'=>'badge-primary'][$this->category] ?? 'badge-ghost';
    }
}
