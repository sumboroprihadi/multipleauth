<?php

namespace App\Http\Controllers\AuthAdmin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;

class AdminLoginController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest:admin',['except' => ['logout']]);
    }

    public function showLoginForm()
    {
        return view('auth-admin.login');
    }

    public function login(Request $req)
    {
        $this->validate($req, [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if(Auth::guard('admin')->attempt(['email' => $req->email, 'password' => $req->password], $req->remember)) {
            // if successful, then redirect to their intended location
            // return redirect()->intended(route('admin'));
            return redirect()->route('admin');
        }

        // if unsuccessful, then redirect back to the login with the form data
        return redirect()->back()->withInput($req->only('email'));
    }

    public function logout(Request $req)
    {
        Auth::guard('admin')->logout();
        $req->session()->invalidate();   
        return redirect()->route('admin');
    }


}
