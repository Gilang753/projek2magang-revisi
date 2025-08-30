<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('admin.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        // Cek kredensial sederhana (bisa diganti dengan database)
        if ($credentials['username'] === 'admin' && $credentials['password'] === 'admin123') {
            session(['admin_logged_in' => true]);
            return redirect()->route('menus.index');
        }

        return back()->withErrors(['login' => 'Username atau password salah']);
    }

    public function logout(Request $request)
    {
        session()->forget('admin_logged_in');
        return redirect()->route('user.index');
    }
}