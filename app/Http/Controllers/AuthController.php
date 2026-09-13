<?php

namespace App\Http\Controllers;

use App\Mail\RegistrationOtpMail;
use App\Models\EmailVerification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'required|string|max:20',
            'password' => 'required|string|min:6|confirmed',
            'profile_image' => 'nullable|image|max:2048',
        ]);

        // Check if the email belongs to an existing user
        $existingEmailUser = User::where('email', $validated['email'])->first();

        if ($existingEmailUser) {
            if ($existingEmailUser->email_verified_at) {
                return back()->withErrors([
                    'email' => 'This email is already registered.',
                ]);
            }

            $existingEmailUser->delete();
        }

        // Check if the phone belongs to an existing user
        $existingPhoneUser = User::where('phone', $validated['phone'])->first();

        if ($existingPhoneUser) {
            if ($existingPhoneUser->email_verified_at) {
                return back()->withErrors([
                    'phone' => 'This phone number is already registered.',
                ]);
            }

            $existingPhoneUser->delete();
        }

        $profile_image = null;

        if ($request->hasFile('profile_image')) {
            $profile_image = $request
                ->file('profile_image')
                ->store('profile-images', 'public');
        }

        $user = User::create([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'password' => $validated['password'],
            'profile_image' => $profile_image,
        ]);

        $user->assignRole('customer');

        $otp = (string) random_int(100000, 999999);

        EmailVerification::create([
            'user_id' => $user->id,
            'otp' => $otp,
            'purpose' => 'registration',
            'expires_at' => now()->addMinutes(10),
        ]);

        Auth::login($user);

        Mail::to($user->email)->send(
            new RegistrationOtpMail($otp)
        );

        return redirect()->route('verify.email');
    }

    public function login(Request $request)
    {
        // ==================== Validate Login ====================

        $validated = $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ]);

        // ==================== Find User ====================

        $user = User::where('email', $validated['login'])
            ->orWhere('phone', $validated['login'])
            ->first();

        // ==================== Check Credentials ====================

        if (!$user || !Hash::check(
            $validated['password'],
            $user->password
        )) {
            return back()->withErrors([
                'login' => 'The email/phone or password is incorrect.',
            ]);
        }

        // ==================== Check Email Verification ====================

        if (!$user->email_verified_at) {
            Auth::login($user);

            return redirect()->route('verify.email');
        }

        // ==================== Login User ====================

        Auth::login($user);

        $request->session()->regenerate();

        return redirect()->route('home');
    }

    public function logout(Request $request)
    {
        // ==================== Logout ====================

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
