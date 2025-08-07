<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function index(){
        return view('signup');
    }
    public function store(UserRequest $request){
       $userData=$request->validated();
       User::create($userData);
        // Here you would typically create the user in the database
        // User::create($request->validated());
        // return redirect()->route('home')->with('success', 'User registered successfully!');
        return redirect()->back()->with('success', 'User registered successfully!');
    }
    public function login(Request $request){
      
        // Validate the login request
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:8',
        ]);
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials) ) {
            // Authentication passed...
            return redirect(url('profile'));
        }
        
        return redirect()->back()->withErrors(['email' => 'Invalid credentials'])->withInput()
        ->with('error', 'Invalid credentials'); 
    }        
}
