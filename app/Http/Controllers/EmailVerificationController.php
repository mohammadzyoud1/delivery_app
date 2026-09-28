<?php

namespace App\Http\Controllers;

use App\Models\EmailVerification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class EmailVerificationController extends Controller
{
    public function show()
    {
        if (Auth::user()?->email_verified_at) {
            return redirect()->route('home');
        }

        return Inertia::render('Auth/VerifyEmail');
    }

    public function verify(Request $request)
    {
        $validated = $request->validate([
            'otp' => 'required|string|size:6',
        ]);

        $verification = EmailVerification::where('user_id', Auth::id())
            ->where('purpose', 'registration')
            ->whereNull('verified_at')
            ->latest()
            ->first();

        if (!$verification) {
            return back()->withErrors([
                'otp' => 'Verification code not found.',
            ]);
        }

        if ($verification->expires_at->isPast()) {
            return back()->withErrors([
                'otp' => 'Verification code has expired.',
            ]);
        }

        if ($verification->otp !== $validated['otp']) {
            return back()->withErrors([
                'otp' => 'Invalid verification code.',
            ]);
        }

        $verification->update([
            'verified_at' => now(),
        ]);

        $user = Auth::user();

        $user->update([
            'email_verified_at' => now(),
        ]);

        return redirect()->route('home');
    }
}
