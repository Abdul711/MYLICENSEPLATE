<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function index()
    {
        return view('signup');
    }
    public function store(UserRequest $request)
    {
        $userData = $request->validated();
        User::create($userData);
        // Here you would typically create the user in the database
        // User::create($request->validated());
        // return redirect()->route('home')->with('success', 'User registered successfully!');
        return redirect()->back()->with('success', 'User registered successfully!');
    }
    public function login(Request $request)
    {

        // Validate the login request
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:8',
        ]);
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            // Authentication passed...
            return redirect(url('profile'));
        }

        return redirect()->back()->withErrors(['email' => 'Invalid credentials'])->withInput()
            ->with('error', 'Invalid credentials');
    }
    public function logout()
    {
        Auth::logout();
        return redirect('/')->with('success', 'Logged out successfully');
    }
    public function update(Request $request)
    {
        $request->validate([
            'name'   => 'required|string|max:255',
            'email'  => 'required|email|max:255|unique:users,email,' . Auth::id(),
            'mobile' => 'nullable|string|max:20',
        ]);

        $user = User::findOrFail(Auth::id());

        // Replace leading 0 with +92
        $mobile = $request->mobile;
        if (!empty($mobile)) {
            $mobile = preg_replace('/\s+/', '', $mobile); // Remove spaces
            if (preg_match('/^0\d+$/', $mobile)) {
                $mobile = '+92' . substr($mobile, 1);
            }
        }

        $user->name   = $request->name;
        $user->email  = $request->email;
        $user->mobile = $mobile ?: null;

        $user->save();



        return redirect()->route('profile')->with('success', 'Profile updated successfully.');
    }
    public function loginform()
    {
        return view("admin.login");
    }
    public function adminLogin(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        if (Auth::guard('backpack')->attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended(backpack_url('dashboard'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }
}
