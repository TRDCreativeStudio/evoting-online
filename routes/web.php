<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\Validation;
use App\Http\Controllers\VoteController;
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

// Route::get('/choose', [Validation::class, 'choose'])->name('choose');
Route::get('/result', [Validation::class, 'result'])->name('result');
Route::get('/choose-new', [Validation::class, 'choose'])->name('choose');

// Sending vote number result
Route::get('/result', [VoteController::class, 'index'])->name('result');
// Route::get('api/result', [VoteController::class, 'countUsers'])->name('progress');


// Route::get('/login', function() {
//     return view('login');
// });

Route::get('/thanks', function() {
    $query = User::where('id', auth()->user()->id)->update(['password' => NULL]);
    
    Session::flush();
    Auth::logout();

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

    return response()->json([
        'status' => 'ok',
        'php' => PHP_VERSION,
        'app_key_set' => !empty(config('app.key')),
        'storage_path' => storage_path(),
        'storage_writable' => is_writable(storage_path()),
        'db' => $dbStatus,
    ]);
});