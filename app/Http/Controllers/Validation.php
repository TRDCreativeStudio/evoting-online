<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\UserVote;

class Validation extends Controller
{
    //anticipate user to go back
    public function __construct(){
        $this->middleware(function ($request, $next) {
            return $next($request)
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
        });
    }

    
    public function choose(){
        if (!Auth::check()) {
            return redirect()->route('index');
        }
        return view('choose-new');
        
    }

    public function result(){
        if (!Auth::check() || auth()->user()->role == 'student') {
            return redirect()->route('index');
        }
        return view('result');
    }

    public function sendVote(Request $request)
    {
        $request->validate([
            'candidate_id' => 'required|between:1,3',
            'nis' => 'required',
        ]);

        $user = Auth::user();
        if (!$user || $user->nis != $request->nis || $user->password === null) {
            return response()->json(['message' => 'Anda tidak memiliki hak akses atau sudah memilih!'], 403);
        }

        \Illuminate\Support\Facades\DB::transaction(function () use ($request, $user) {
            UserVote::create([
                'nis_vote' => $user->nis,
                'vote_number' => $request->candidate_id,
            ]);

            $user->update([
                'password' => null,
                'voted_at' => now(),
            ]);
        });

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['status' => 'success']);
    }
}
