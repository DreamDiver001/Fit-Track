<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Goal extends Model {
    use HasFactory;
    protected $fillable = ['user_id','title','goal_type','start_value','target_value','current_value','unit','deadline','status','notes'];
    protected $casts    = ['deadline'=>'date','start_value'=>'decimal:2','target_value'=>'decimal:2','current_value'=>'decimal:2'];
    public function user() { return $this->belongsTo(User::class); }
    public function getProgressPercentAttribute(): int {
        if (!$this->target_value) return 0;
        if ($this->goal_type === 'weight_loss') {
            $start = $this->start_value ?? $this->current_value;
            if (!$start || $start <= $this->target_value) return 0;
            $totalToLose = $start - $this->target_value;
            $lost        = $start - ($this->current_value ?? $start);
            $pct         = $totalToLose > 0 ? round(($lost / $totalToLose) * 100) : 0;
        } else {
            if (!$this->current_value) return 0;
            $pct = round(($this->current_value / $this->target_value) * 100);
        }
        return (int) min(100, max(0, $pct));
    }
    public function getStatusColorAttribute(): string {
        return ['in_progress'=>'badge-info','completed'=>'badge-success','abandoned'=>'badge-ghost'][$this->status] ?? 'badge-ghost';
    }
    public function getGoalTypeLabelAttribute(): string {
        return ['weight_loss'=>'Weight Loss','endurance'=>'Endurance','strength'=>'Strength','habit'=>'Habit','custom'=>'Custom'][$this->goal_type] ?? ucfirst($this->goal_type);
    }
}
