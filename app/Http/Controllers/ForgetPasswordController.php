<?php

namespace App\Http\Controllers;

use DB;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;

class ForgetPasswordController extends Controller
{
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email|',
        ]);
          $token = Str::random(6);
Mail::send('emails.reset', [
    'token' => $token,
    'email' => $request->email,
], function ($message) use ($request) {
    $message->to($request->email);
    $message->subject('Password Reset Link');
});

DB::table('password_reset_tokens')->updateOrInsert(
    ['email' => $request->email], // Condition: look for existing email
    [
        'token' => $token,
        'created_at' => now()
    ]
);
        // Logic to send reset link email
        // For example, using Laravel's Password broker:
        // Password::sendResetLink($request->only('email'));
        return back()->with('status', 'Password reset link sent to your email address.');
     
    }

    public function showResetForm($token)
    {
        return view('resetpassword', ['token' => $token]);
    }

    public function reset(Request $request)
    {
     $validated=   $request->validate([

            'password' => 'required|min:8|confirmed',
            'token' => 'required',
            "password_confirmation" => 'required|min:8',
        ]);
       
     $data = collect($validated)->only(['password', 'token'])->toArray();
        $email = DB::table('password_reset_tokens')
            ->where('token', $data['token'])
            ->value('email');       

        if (!$email) {
            return back()->withErrors(['token' => 'Invalid token.']);
        }

        $user = User::where('email', $email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'No user found with this email address.']);
        }

        $user->password = Hash::make($data['password']);
        $user->save();

        // Optionally, delete the token after successful reset
        DB::table('password_reset_tokens')->where('token', $data['token'])->delete();
        
        return redirect()->route('login')->with('status', 'Password has been reset successfully.');
    }   // Logic to reset the password
    // ...
}
