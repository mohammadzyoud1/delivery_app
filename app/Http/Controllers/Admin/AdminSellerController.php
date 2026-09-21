<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;

class AdminSellerController extends Controller
{
    // ==================== Admin Sellers ====================

    public function index()
    {
        // ==================== Load Sellers ====================

        $sellers = User::query()
            ->whereHas('roles', function ($query) {
                $query->where('name', 'seller');
            })
            ->with([
                'roles',
                'products',
            ])
            ->latest()
            ->get([
                'id',
                'first_name',
                'last_name',
                'email',
                'phone',
                'profile_image',
                'email_verified_at',
                'created_at',
            ]);

        // ==================== Return Sellers Page ====================

        return inertia('Admin/Sellers', [
            'sellers' => $sellers,
        ]);
    }
}
