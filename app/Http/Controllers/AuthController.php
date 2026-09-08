<?php

namespace App\Http\Controllers;

use App\Mail\RegistrationOtpMail;
use App\Models\EmailVerification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
}
