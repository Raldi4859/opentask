<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function login()
    {
        if (Auth::check()) {
            return redirect('home');
        }else{
            return view('login');
        }
    }

    public function actionlogin(Request $request)
    {
        $data = [
            'email' => $request->input('email'),
            'password' => $request->input('password'),
        ];

        if (Auth::Attempt(['email' => $request->email, 
        'password' => $request->password])) {
            return redirect('home');
        }else{
            return redirect()->back()->withInput()->withErrors(['email' => 'Email atau Password Salah']);
        }
    }

    public function actionlogout()
    {
        Auth::logout();
        return redirect('/');
    }
}