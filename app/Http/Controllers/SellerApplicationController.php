<?php

namespace App\Http\Controllers;

use App\Models\SellerApplication;
use Illuminate\Http\Request;

class SellerApplicationController extends Controller
{
    // ==================== Seller Application Page ====================

    // ==================== Seller Application Page ====================

    public function create(Request $request)
    {
        // ==================== Find Current Application ====================

        $application = SellerApplication::query()
            ->where('user_id', $request->user()->id)
            ->latest()
            ->first();

        // ==================== Check Actual Seller Role ====================

        $isSeller = $request->user()->hasRole('seller');

        // ==================== Return Application Page ====================

        return inertia('Seller/Apply', [
            'application' => $application,
            'isSeller' => $isSeller,
        ]);
    }

    // ==================== Store Seller Application ====================

    public function store(Request $request)
    {
        // ==================== Validate Application ====================

        $validated = $request->validate([
            'reason' => ['required', 'string', 'max:2000'],
        ]);

        // ==================== Check Existing Active Application ====================

        $hasActiveApplication = SellerApplication::query()
            ->where('user_id', $request->user()->id)
            ->where('status', 'pending')
            ->exists();

        if ($hasActiveApplication) {
            return back()->with(
                'error',
                'You already have a pending seller application.',
            );
        }

        // ==================== Create Application ====================

        SellerApplication::create([
            'user_id' => $request->user()->id,
            'status' => 'pending',
            'reason' => $validated['reason'],
        ]);

        // ==================== Return Success ====================

        return redirect()
            ->route('home')
            ->with(
                'success',
                'Your seller application has been submitted successfully.',
            );
    }
}
