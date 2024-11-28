<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AccountInfo;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginVerifyController extends Controller
{

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'account_user' => 'required',
            'account_password' => 'required'
        ]);
    
        if ($validator->fails()) {
            return back()->withErrors($validator->errors());
        }
    
        $credentials = [
            'account_user' => $request->account_user,
            'account_password' => $request->account_password
        ];
    
        $account = AccountInfo::where('account_user', $credentials['account_user'])->first();
    
        if ($account && Hash::check($credentials['account_password'], $account->account_password)) {
            // Set a session variable upon successful login
            session(['logged_in_user' => $account->account_user]);
            
            // Simply log the user in without modifying roles
            Auth::login($account);
            return redirect()->intended('dashboard');
        }
    
        return back()->withErrors(['login_error' => 'Invalid credentials']);
    }

    


    public function showLoginForm()
    {
        return response()
        ->view('welcome')
        ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
        ->header('Pragma', 'no-cache')
        ->header('Expires', 'Sat, 01 Jan 2000 00:00:00 GMT');
        
        return view('welcome');
    }

    public function dashboard(){
            return view('estab_pages/estab_dashboard');
        
    }

    public function logout()
    {
        Auth::logout();
        session()->forget('logged_in_user');
        return redirect('/');
}

}