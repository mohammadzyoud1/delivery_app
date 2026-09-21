<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\SellerApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminSellerApplicationController extends Controller
{
    // ==================== Seller Applications ====================

    public function index()
    {
        // ==================== Load Applications ====================

        $applications = SellerApplication::query()
            ->with([
                'user',
                'reviewer',
            ])
            ->latest()
            ->get();

        // ==================== Return Applications Page ====================

        return inertia('Admin/SellerApplications', [
            'applications' => $applications,
        ]);
    }

    // ==================== Seller Application Details ====================

    public function show(SellerApplication $sellerApplication)
    {
        // ==================== Load Application Data ====================

        $sellerApplication->load([
            'user',
            'reviewer',
        ]);

        // ==================== Return Details Page ====================

        return inertia('Admin/SellerApplicationDetails', [
            'application' => $sellerApplication,
        ]);
    }

    // ==================== Approve Seller Application ====================

    public function approve(
        Request $request,
        SellerApplication $sellerApplication
    ) {
        DB::transaction(function () use ($request, $sellerApplication) {
            // ==================== Lock Application ====================

            $sellerApplication = SellerApplication::query()
                ->lockForUpdate()
                ->with('user')
                ->findOrFail($sellerApplication->id);

            // ==================== Validate Application Status ====================

            if ($sellerApplication->status !== 'pending') {
                abort(
                    422,
                    'Only pending seller applications can be approved.'
                );
            }

            // ==================== Get Seller Role ====================

            $sellerRole = Role::query()
                ->where('name', 'seller')
                ->firstOrFail();

            // ==================== Add Seller Role ====================

            $sellerApplication
                ->user
                ->roles()
                ->syncWithoutDetaching([$sellerRole->id]);

            // ==================== Approve Application ====================

            $sellerApplication->update([
                'status' => 'approved',
                'reviewer' => $request->user()->id,
                'reviewed_at' => now(),
            ]);
        });

        // ==================== Return Success ====================

        return back()->with(
            'success',
            "Seller application #{$sellerApplication->id} has been approved."
        );
    }

    // ==================== Reject Seller Application ====================

    public function reject(
        Request $request,
        SellerApplication $sellerApplication
    ) {
        // ==================== Validate Rejection Reason ====================

        $validated = $request->validate([
            'rejection_reason' => [
                'required',
                'string',
                'max:1000',
            ],
        ]);

        DB::transaction(function () use (
            $request,
            $sellerApplication,
            $validated
        ) {
            // ==================== Lock Application ====================

            $sellerApplication = SellerApplication::query()
                ->lockForUpdate()
                ->findOrFail($sellerApplication->id);

            // ==================== Validate Application Status ====================

            if ($sellerApplication->status !== 'pending') {
                abort(
                    422,
                    'Only pending seller applications can be rejected.'
                );
            }

            // ==================== Reject Application ====================

            $sellerApplication->update([
                'status' => 'rejected',
                'rejection_reason' => $validated['rejection_reason'],
                'reviewer' => $request->user()->id,
                'reviewed_at' => now(),
            ]);
        });

        // ==================== Return Success ====================

        return back()->with(
            'success',
            "Seller application #{$sellerApplication->id} has been rejected."
        );
    }
}
