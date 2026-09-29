<?php

namespace App\Http\Controllers;

use App\Mail\PasswordResetMail;
use App\Mail\RegistrationOtpMail;
use App\Models\EmailVerification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
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
            'notification_preferences' => [
                'messages' => true,
                'orders' => true,
            ],
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

    // ==================== Forgot Password ====================

    public function forgotPassword(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $validated['email'])->first();

        // ==================== Generic Response ====================
        // Do not reveal whether this email belongs to an account.

        if (!$user) {
            return back()->with(
                'success',
                'If an account exists for this email, a password reset link has been sent.'
            );
        }

        // ==================== Generate Secure Token ====================

        $token = Str::random(64);

        $hashedToken = hash('sha256', $token);

        // ==================== Replace Existing Token ====================

        DB::table('password_reset_tokens')
            ->where('email', $user->email)
            ->delete();

        // ==================== Store Token ====================

        DB::table('password_reset_tokens')->insert([
            'email' => $user->email,
            'token' => $hashedToken,
            'created_at' => now(),
        ]);

        // ==================== Generate Reset URL ====================

        $resetUrl = route('password.reset', [
            'token' => $token,
        ]);

        // ==================== Send Reset Email ====================

        Mail::to($user->email)->send(
            new PasswordResetMail($resetUrl)
        );

        // ==================== Generic Response ====================

        return back()->with(
            'success',
            'If an account exists for this email, a password reset link has been sent.'
        );
    }

    // ==================== Show Reset Password ====================

    public function showResetPassword(string $token)
    {
        $hashedToken = hash('sha256', $token);

        $resetToken = DB::table('password_reset_tokens')
            ->where('token', $hashedToken)
            ->where('created_at', '>=', now()->subMinutes(60))
            ->first();

        // ==================== Validate Token ====================

        if (!$resetToken) {
            return redirect()
                ->route('password.request')
                ->withErrors([
                    'email' => 'This password reset link is invalid or expired.',
                ]);
        }

        // ==================== Show Reset Page ====================

        return Inertia::render('Auth/ResetPassword', [
            'token' => $token,
        ]);
    }

    // ==================== Reset Password ====================

    public function resetPassword(Request $request)
    {
        // ==================== Validate Request ====================

        $validated = $request->validate([
            'token' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ]);

        // ==================== Find Valid Token ====================

        $hashedToken = hash('sha256', $validated['token']);

        $resetToken = DB::table('password_reset_tokens')
            ->where('token', $hashedToken)
            ->where('created_at', '>=', now()->subMinutes(60))
            ->first();

        if (!$resetToken) {
            return back()->withErrors([
                'password' => 'This password reset link is invalid or expired.',
            ]);
        }

        // ==================== Find User ====================

        $user = User::where('email', $resetToken->email)->first();

        if (!$user) {
            return back()->withErrors([
                'password' => 'This password reset link is invalid or expired.',
            ]);
        }

        // ==================== Update Password ====================

        $user->update([
            'password' => $validated['password'],
        ]);

        // ==================== Consume Reset Token ====================

        DB::table('password_reset_tokens')
            ->where('token', $hashedToken)
            ->delete();

        // ==================== Redirect To Login ====================

        return redirect()
            ->route('login')
            ->with(
                'success',
                'Your password has been reset successfully. Please log in.'
            );
    }
}
