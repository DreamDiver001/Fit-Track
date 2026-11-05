<?php
namespace App\Http\Controllers;
use App\Models\Workout;

class HomeController extends Controller {
    public function index() {
        $workouts = Workout::where('is_public', true)->latest()->take(6)->get();
        return view('home', compact('workouts'));
    }
}
