<?php

namespace App\Http\Controllers\AuthAdmin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Mail;
use App\Mail\SendMail;
use App\Mail\SendMailHandler;


use Validator;

use Carbon\Carbon;
use App\Admin;
use App\NotificationEmail;
use App\CurrentCall;
use Hash;


class PasswordResetController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest:admin',['except' => ['logout']]);
    }

    public function index()
    {  
        return view('auth-admin.forgot-password');
    }

    public function sendResetEmail(Request $req)
    {
        $validator = Validator::make($req->all(),[
            'email' => 'required|email'
        ]);

        if($validator->fails()){
            return redirect()->back()->withErrors($validator);
        }

        $admin = Admin::where('email', $req->email)->first();

        if(empty($admin)){
            return redirect()->back()->withErrors(['email' => 'Email '.$req->email.' not found!']);
        }else{

            $token = strtolower(str_random(64));

            DB::table('password_resets')->insert([
                'email'      => $req->email,
                'token'      => $token,
                'created_at' => Carbon::now()
            ]); 

            $dataEmail = [
                'subject' => 'Reset Password',
                'view' => 'mail.admin-reset',
                'params' => [
                    'name' => $admin->name,
                    'email' => $req->email,
                    'token' => $token
                ]
            ];
            
            Mail::to($req->email)->queue(new SendMailHandler($dataEmail));
    
            return redirect()->route('admin.forgot-password')->with('message','we sent you reset password link, please check your email..'); 
        }
    }

    public function verify($email,$token)
    {
        $verify = DB::table('password_resets')
                    ->select('email', 'token', 'created_at')
                    ->where('email',$email)
                    ->where('token',$token)
                    ->orderBy('created_at', 'DESC')
                    ->first();

        if(isset($verify)){
            $expiredLimit   = date('Y-m-d H:i:s', strtotime('+30 minutes', strtotime($verify->created_at)));
            $now            = date('Y-m-d H:i:s');
    
            if($now > $expiredLimit){
                return redirect()->route('forgot-password')->with('message','sorry, session reset password has expired');
            } else {
                return view('auth-admin.reset-password',[
                    'email' => $email,
                    'token' => $token
                ]);
            }

        }else{
            return redirect()->route('admin');
        }
    }

    public function updatePassword(Request $req)
    {
        $validator = Validator::make($req->all(),[
            'email'                 => ['required', 'string'],
            'password'              => ['required', 'string', 'min:8'],
            'password_confirmation' => ['required', 'same:password'],
            'token'                 => ['required', 'string']
        ]);
        
        if($validator->fails()){
            return redirect()->route('admin.reset.password.verify',['email'=>$req->email,'token'=>$req->token])->withErrors($validator);
        }else{

            $admin = Admin::where('email', $req->email)->first();
            $admin->password = Hash::make($req->password);

            $admin->save();

            return redirect()->route('admin')->with('message','Update password successfully!'); 
        }
    }
}
