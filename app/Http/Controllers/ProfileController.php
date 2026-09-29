<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProfileController extends Controller
{
    // ==================== Update Profile Information ====================

    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
        ]);

        // ==================== Update User ====================

        $user->update([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'phone' => $validated['phone'],
        ]);

        // ==================== Success ====================

        return back()->with(
            'success',
            'Your profile information has been updated successfully.'
        );
    }

    // ==================== Update Notification Preferences ====================

    public function updateNotifications(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'messages' => 'required|boolean',
            'orders' => 'required|boolean',
        ]);

        // ==================== Save Preferences ====================

        $user->update([
            'notification_preferences' => [
                'messages' => $validated['messages'],
                'orders' => $validated['orders'],
            ],
        ]);

        // ==================== Success ====================

        return back()->with(
            'success',
            'Your notification preferences have been updated successfully.'
        );
    }

    // ==================== Update Profile Picture ====================

    public function updatePicture(Request $request)
    {
        $user = $request->user();

        // ==================== Validate Image ====================

        $request->validate([
            'profile_image' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        // ==================== Store Image ====================

        $profileImage = $request
            ->file('profile_image')
            ->store('profile-images', 'public');

        // ==================== Update User ====================

        $user->update([
            'profile_image' => $profileImage,
        ]);

        // ==================== Success ====================

        return back()->with(
            'success',
            'Your profile picture has been updated successfully.'
        );
    }
}
