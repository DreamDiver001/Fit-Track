<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User; use App\Models\Workout; use App\Models\Exercise;
use App\Models\WorkoutLog; use App\Models\Goal;

class DatabaseSeeder extends Seeder {
    public function run(): void {
        $admin = User::create(['name'=>'Admin','email'=>'admin@fittrack.com','password'=>Hash::make('password'),'is_admin'=>true]);
        $user  = User::create(['name'=>'John Doe','email'=>'user@fittrack.com','password'=>Hash::make('password'),'is_admin'=>false]);

        $exData = [
            ['name'=>'Barbell Bench Press','muscle_group'=>'chest',   'equipment'=>'barbell',   'description'=>'Classic chest pressing movement.','instructions'=>'Lie flat on bench. Grip bar wider than shoulder width. Lower bar to chest. Press back up explosively.'],
            ['name'=>'Barbell Back Squat', 'muscle_group'=>'legs',    'equipment'=>'barbell',   'description'=>'King of leg exercises.','instructions'=>'Bar on upper traps. Squat down until thighs are parallel to floor. Drive through heels to stand.'],
            ['name'=>'Deadlift',           'muscle_group'=>'back',    'equipment'=>'barbell',   'description'=>'Full-body compound lift.','instructions'=>'Stand with bar over mid-foot. Hinge at hips, grip bar. Drive hips forward to lockout.'],
            ['name'=>'Pull-Up',            'muscle_group'=>'back',    'equipment'=>'bodyweight','description'=>'Upper body pulling movement.','instructions'=>'Hang from bar with overhand grip. Pull chin above bar. Lower with control.'],
            ['name'=>'Plank',              'muscle_group'=>'core',    'equipment'=>'bodyweight','description'=>'Core stability exercise.','instructions'=>'Hold push-up position on forearms. Keep body in straight line. Breathe steadily.'],
            ['name'=>'Dumbbell Curl',      'muscle_group'=>'arms',    'equipment'=>'dumbbell',  'description'=>'Bicep isolation exercise.','instructions'=>'Stand with dumbbells at sides. Curl up contracting biceps. Lower slowly.'],
            ['name'=>'Overhead Press',     'muscle_group'=>'shoulders','equipment'=>'barbell',  'description'=>'Shoulder pressing movement.','instructions'=>'Press barbell from shoulders to lockout overhead. Keep core tight.'],
            ['name'=>'Burpees',            'muscle_group'=>'full_body','equipment'=>'bodyweight','description'=>'High-intensity full body movement.','instructions'=>'Squat down, jump feet back, do push-up, jump feet forward, jump up.'],
            ['name'=>'Jump Squats',        'muscle_group'=>'legs',    'equipment'=>'bodyweight','description'=>'Plyometric leg exercise.','instructions'=>'Squat down, explode into a jump, land softly and repeat.'],
            ['name'=>'Mountain Climbers',  'muscle_group'=>'core',    'equipment'=>'bodyweight','description'=>'Dynamic core and cardio exercise.','instructions'=>'In plank position, alternate driving knees to chest rapidly.'],
        ];
        $exs = [];
        foreach ($exData as $e) $exs[] = Exercise::create($e);

        $wkData = [
            ['title'=>'Full Body HIIT',   'description'=>'High intensity interval training for total body burn.','category'=>'hiit',       'difficulty'=>'hard',  'estimated_duration'=>45,'estimated_calories'=>450,'is_public'=>true],
            ['title'=>'Push Day',         'description'=>'Chest, shoulders, and triceps strength session.',     'category'=>'strength',    'difficulty'=>'medium','estimated_duration'=>60,'estimated_calories'=>300,'is_public'=>true],
            ['title'=>'Morning Yoga Flow','description'=>'Gentle yoga for flexibility and mindfulness.',        'category'=>'flexibility', 'difficulty'=>'easy',  'estimated_duration'=>30,'estimated_calories'=>150,'is_public'=>true],
            ['title'=>'Leg Day',          'description'=>'Complete lower body strength workout.',               'category'=>'strength',    'difficulty'=>'hard',  'estimated_duration'=>70,'estimated_calories'=>400,'is_public'=>true],
            ['title'=>'Core & Abs',       'description'=>'Core strengthening and stability workout.',           'category'=>'core',        'difficulty'=>'medium','estimated_duration'=>25,'estimated_calories'=>200,'is_public'=>true],
        ];
        $wks = [];
        foreach ($wkData as $w) $wks[] = Workout::create($w);

        // Many-to-many: attach exercises to workouts
        $wks[0]->exercises()->attach([
            $exs[7]->id=>['sets'=>4,'reps'=>15,'rest_seconds'=>30,'sort_order'=>1],
            $exs[8]->id=>['sets'=>4,'reps'=>20,'rest_seconds'=>30,'sort_order'=>2],
            $exs[9]->id=>['sets'=>3,'reps'=>null,'rest_seconds'=>20,'sort_order'=>3],
            $exs[4]->id=>['sets'=>3,'reps'=>null,'rest_seconds'=>30,'sort_order'=>4],
        ]);
        $wks[1]->exercises()->attach([
            $exs[0]->id=>['sets'=>4,'reps'=>10,'rest_seconds'=>90,'sort_order'=>1],
            $exs[6]->id=>['sets'=>4,'reps'=>10,'rest_seconds'=>90,'sort_order'=>2],
            $exs[5]->id=>['sets'=>3,'reps'=>12,'rest_seconds'=>60,'sort_order'=>3],
        ]);
        $wks[2]->exercises()->attach([
            $exs[4]->id=>['sets'=>3,'reps'=>60,'rest_seconds'=>30,'sort_order'=>1],
        ]);
        $wks[3]->exercises()->attach([
            $exs[1]->id=>['sets'=>5,'reps'=>8,'rest_seconds'=>120,'sort_order'=>1],
            $exs[8]->id=>['sets'=>4,'reps'=>15,'rest_seconds'=>60,'sort_order'=>2],
        ]);
        $wks[4]->exercises()->attach([
            $exs[4]->id=>['sets'=>4,'reps'=>60,'rest_seconds'=>30,'sort_order'=>1],
            $exs[9]->id=>['sets'=>3,'reps'=>30,'rest_seconds'=>20,'sort_order'=>2],
        ]);

        // Logs for demo user
        WorkoutLog::create(['user_id'=>$user->id,'workout_id'=>$wks[0]->id,'logged_at'=>now()->subDays(0)->toDateString(),'duration_minutes'=>45,'calories_burned'=>430,'effort_rating'=>'great','notes'=>'Felt amazing today!','tags'=>'morning,personal_record']);
        WorkoutLog::create(['user_id'=>$user->id,'workout_id'=>$wks[1]->id,'logged_at'=>now()->subDays(1)->toDateString(),'duration_minutes'=>62,'calories_burned'=>310,'effort_rating'=>'okay','notes'=>'Solid push session.','tags'=>'']);
        WorkoutLog::create(['user_id'=>$user->id,'workout_id'=>$wks[2]->id,'logged_at'=>now()->subDays(2)->toDateString(),'duration_minutes'=>30,'calories_burned'=>155,'effort_rating'=>'great','notes'=>'Great morning stretch.','tags'=>'morning']);

        // Goals for demo user
        Goal::create(['user_id'=>$user->id,'title'=>'Lose 5kg by June 2026','goal_type'=>'weight_loss','target_value'=>70,'current_value'=>72,'unit'=>'kg','deadline'=>'2026-06-01','status'=>'in_progress','notes'=>'Target: 70kg']);
        Goal::create(['user_id'=>$user->id,'title'=>'Run 5km under 25 minutes','goal_type'=>'endurance','target_value'=>25,'current_value'=>28,'unit'=>'min','deadline'=>'2026-05-15','status'=>'in_progress','notes'=>'Current best: 28 min']);
    }
}
