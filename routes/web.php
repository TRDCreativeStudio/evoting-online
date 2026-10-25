<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\Validation;
use App\Http\Controllers\VoteController;
use App\Http\Controllers\AdminStudentController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\User;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('login');
})->name('index');

// Route::get('/login', function() {
//     return view('login');
// });
// 

//Authentication
Route::post('/login', [LoginController::class, 'login_aksi'])->name('login');
Route::get('/logout', [LoginController::class, 'logout_aksi'])->name('logout');

Route::post('/api/vote', [Validation::class, 'sendVote'])->name('vote');
Route::post('/vote', [Validation::class, 'sendVote']);

// Route::get('/choose', [Validation::class, 'choose'])->name('choose');
Route::get('/choose-new', [Validation::class, 'choose'])->name('choose');

// Sending vote number result (Admin)
Route::get('/result', [VoteController::class, 'index'])->name('result');

// Admin Student & Voter Management Routes
Route::prefix('admin')->group(function () {
    Route::get('/students', [AdminStudentController::class, 'index'])->name('admin.students');
    Route::post('/students/import', [AdminStudentController::class, 'importCsv'])->name('admin.students.import');
    Route::get('/students/export', [AdminStudentController::class, 'exportCsv'])->name('admin.students.export');
    Route::post('/students/regenerate-tokens', [AdminStudentController::class, 'regenerateTokens'])->name('admin.students.regenerate_tokens');
    Route::post('/students/reset-votes', [AdminStudentController::class, 'resetVotes'])->name('admin.students.reset_votes');
    Route::post('/students/delete-all', [AdminStudentController::class, 'deleteAllStudents'])->name('admin.students.delete_all');
    Route::delete('/students/{id}', [AdminStudentController::class, 'deleteStudent'])->name('admin.students.delete');
    Route::post('/save-admin', [AdminStudentController::class, 'saveAdmin'])->name('admin.save');
});
// Route::get('api/result', [VoteController::class, 'countUsers'])->name('progress');


// Route::get('/login', function() {
//     return view('login');
// });

Route::get('/thanks', function() {
    return view('thanks');
})->name('thanks');

Route::get('/loginVote', function() {
    return view('loginVote');
});


// CSRF Token Auto-refresh
Route::get('/refresh-csrf', function () {
    return csrf_token();
});

// Helper route to run migrations in serverless environment (Vercel)
Route::get('/migrate-db', function () {
    try {
        \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
        return '<pre>' . \Illuminate\Support\Facades\Artisan::output() . "\nMigration Successful!</pre>";
    } catch (\Throwable $e) {
        return '<pre>Migration Error: ' . $e->getMessage() . '</pre>';
    }
});

// Diagnostic check route
Route::get('/debug-check', function () {
    try {
        \Illuminate\Support\Facades\DB::connection()->getPdo();
        $dbStatus = 'Connected to database: ' . \Illuminate\Support\Facades\DB::connection()->getDatabaseName();
    } catch (\Throwable $e) {
        $dbStatus = 'Database connection error: ' . $e->getMessage();
    }

    $votes = \App\Models\UserVote::all();
    $students = \App\Models\User::where('role', 'student')->get(['nis', 'name', 'password']);
    
    $voteCounts = \App\Models\UserVote::select('vote_number', \Illuminate\Support\Facades\DB::raw('count(*) as total'))
        ->groupBy('vote_number')
        ->get();

    $alreadyVote = \Illuminate\Support\Facades\DB::table('users')
        ->join('user_votes', 'users.nis', '=', 'user_votes.nis_vote')
        ->where('users.role', 'student')
        ->count();

    return response()->json([
        'status' => 'ok',
        'db' => $dbStatus,
        'user_votes_count' => $votes->count(),
        'user_votes' => $votes,
        'student_count' => $students->count(),
        'students_voted_null_password' => $students->whereNull('password')->count(),
        'vote_counts' => $voteCounts,
        'already_vote_join_count' => $alreadyVote,
    ]);
});