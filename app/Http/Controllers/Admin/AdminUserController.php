<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;

class AdminUserController extends Controller
{
    // ==================== Admin Users ====================

    public function index()
    {
        // ==================== Load Users ====================

        $users = User::query()
            ->with('roles')
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

        // ==================== Return Users Page ====================

        return inertia('Admin/Users', [
            'users' => $users,
        ]);
    }

    // ==================== User Details ====================

    public function show(User $user)
    {
        // ==================== Load User Data ====================

        $user->load([
            'roles',
            'orders' => function ($query) {
                $query->latest();
            },
            'deliveryAssignments' => function ($query) {
                $query->latest();
            },
        ]);

        // ==================== Return User Details Page ====================

        return inertia('Admin/UserDetails', [
            'user' => $user,
        ]);
    }
}
