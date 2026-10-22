<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use App\Models\User;

class LoginController extends Controller
{
    public function login_aksi(Request $request)
    {
        $request->validate([
            'nis' => 'required',
            'token' => 'required',
        ]);

        // Single query fetch for faster performance
        $user = User::where('nis', $request->nis)->first();

        if (!$user) {
            return redirect('/')->with('status', 'Akun anda tidak terdaftar!');
        }

        if ($user->password === null) {
            return redirect('/')->with('status', 'Anda tidak dapat melakukan pemilihan 2 kali!');
        }

        // Direct Hash::check without re-querying the database
        if (!Hash::check($request->token, $user->password)) {
            return redirect('/')->with('status', 'Token anda tidak valid!');
        }

        Auth::login($user);

        if ($user->role === 'admin') {
            return redirect()->route('result');
        }

        return redirect()->route('choose');
    }

    public function logout_aksi()
    {
        Session::flush();
        Auth::logout();
        return redirect()->route('index');
    }
}
