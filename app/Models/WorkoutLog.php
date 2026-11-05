<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkoutLog extends Model {
    use HasFactory;
    protected $fillable = ['user_id','workout_id','logged_at','duration_minutes','calories_burned','effort_rating','notes','tags'];
    protected $casts    = ['logged_at'=>'date'];
    public function user()    { return $this->belongsTo(User::class); }
    public function workout() { return $this->belongsTo(Workout::class); }
    public function getEffortLabelAttribute() {
        return ['great'=>'😄 Great','okay'=>'😐 Okay','tough'=>'😓 Tough'][$this->effort_rating] ?? ucfirst($this->effort_rating);
    }
    public function getTagsArrayAttribute() {
        return $this->tags ? array_values(array_filter(explode(',', $this->tags))) : [];
    }
}
