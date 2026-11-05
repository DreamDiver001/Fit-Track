<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exercise extends Model {
    use HasFactory;
    protected $fillable = ['name','description','muscle_group','equipment','instructions','image_path'];
    public function workouts() { return $this->belongsToMany(Workout::class,'workout_exercises')->withPivot('sets','reps','rest_seconds','sort_order'); }
    public function getMuscleGroupLabelAttribute() {
        return ['chest'=>'Chest','back'=>'Back','legs'=>'Legs','core'=>'Core','arms'=>'Arms','shoulders'=>'Shoulders','full_body'=>'Full Body'][$this->muscle_group] ?? ucfirst($this->muscle_group);
    }
    public function getEquipmentLabelAttribute() {
        return ['barbell'=>'Barbell','dumbbell'=>'Dumbbell','bodyweight'=>'Bodyweight','machine'=>'Machine','cable'=>'Cable','other'=>'Other'][$this->equipment] ?? ucfirst($this->equipment);
    }
}
