<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index()
    {
        return view('admin.index');
    }

    public function changePassword()
    {
        return view('admin.profile.change-password');
    }

    public function submitChange(Request $req)
    {

        if(Auth::Check()){
        }

    }
}
