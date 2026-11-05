<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable {
    use HasFactory, Notifiable;
    protected $fillable = ['name','email','password','is_admin','is_banned'];
    protected $hidden   = ['password','remember_token'];
    protected $casts    = ['email_verified_at'=>'datetime','password'=>'hashed','is_admin'=>'boolean','is_banned'=>'boolean'];
    public function workoutLogs() { return $this->hasMany(WorkoutLog::class); }
    public function goals()       { return $this->hasMany(Goal::class); }
}
