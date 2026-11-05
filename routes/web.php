<?php
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\WorkoutController;
use App\Http\Controllers\ExerciseController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\WorkoutLogController;
use App\Http\Controllers\GoalController;
use App\Http\Controllers\Admin;
use Illuminate\Support\Facades\Route;

Route::get('/',           [HomeController::class,    'index'])->name('home');
Route::get('/workouts',   [WorkoutController::class, 'index'])->name('workouts.index');
Route::get('/workouts/{workout}', [WorkoutController::class,'show'])->name('workouts.show');
Route::get('/exercises',  [ExerciseController::class,'index'])->name('exercises.index');

Route::middleware('guest')->group(function () {
    Route::get('/login',    [AuthenticatedSessionController::class,'create'])->name('login');
    Route::post('/login',   [AuthenticatedSessionController::class,'store']);
    Route::get('/register', [RegisteredUserController::class,'create'])->name('register');
    Route::post('/register',[RegisteredUserController::class,'store']);
});
Route::post('/logout', [AuthenticatedSessionController::class,'destroy'])->name('logout')->middleware('auth');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class,'index'])->name('dashboard');
    Route::resource('logs',  WorkoutLogController::class)->except(['show']);
    Route::resource('goals', GoalController::class)->except(['show']);
});

Route::middleware(['auth','admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [Admin\DashboardController::class,'index'])->name('dashboard');
    Route::get('/users',                    [Admin\UserController::class,'index'])    ->name('users.index');
    Route::post('/users/{user}/toggle-ban', [Admin\UserController::class,'toggleBan'])->name('users.toggleBan');
    Route::delete('/users/{user}',          [Admin\UserController::class,'destroy'])  ->name('users.destroy');
    Route::resource('workouts',  Admin\WorkoutController::class)->except(['show']);
    Route::resource('exercises', Admin\ExerciseController::class)->except(['show']);
});
