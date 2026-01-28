<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Show the form for editing the profile.
     */
    public function edit()
    {
        $user = Auth::user();
        $notifications = json_decode($user->notification_settings ?? '{}', true);
        
        return view('profile.edit', compact('user', 'notifications'));
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Handle profile image upload
        if ($request->hasFile('profile_image')) {
            // Delete old image if exists
            if ($user->profile_image) {
                Storage::disk('public')->delete($user->profile_image);
            }
            
            $imagePath = $request->file('profile_image')->store('profile-images', 'public');
            $validated['profile_image'] = $imagePath;
        }



        $user->update($validated);

        return redirect()->route('profile.edit')
            ->with('success', 'Profile updated successfully!')
            ->with('activeTab', 'profile');
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();

        // Check current password
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.'])
                        ->with('activeTab', 'password');
        }

        // Update password
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('profile.edit')
            ->with('success', 'Password updated successfully!')
            ->with('activeTab', 'password');
    }



    /**
     * Delete the user's account.
     */
    public function destroy(Request $request)
    {
        $request->validate([
            'password' => 'required|string',
        ]);

        $user = Auth::user();

        // Verify password
        if (!Hash::check($request->password, $user->password)) {
            return back()->withErrors(['password' => 'The password is incorrect.'])
                        ->with('activeTab', 'delete');
        }

        // Delete user's wardrobe items and images
        foreach ($user->wardrobes as $item) {
            if ($item->image) {
                Storage::disk('public')->delete($item->image);
            }
            $item->delete();
        }

        // Delete user's recommendations
        $user->recommendations()->delete();

        // Delete profile image if exists
        if ($user->profile_image) {
            Storage::disk('public')->delete($user->profile_image);
        }

        // Delete user
        $user->delete();

        // Logout and redirect
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('status', 'Your account has been deleted successfully.');
    }

    /**
     * Update notification settings.
     */
    public function updateNotifications(Request $request)
    {
        $user = Auth::user();

        $notificationSettings = [
            'email_notifications' => $request->boolean('email_notifications'),
            'push_notifications' => $request->boolean('push_notifications'),
            'outfit_suggestions' => $request->boolean('outfit_suggestions'),
            'new_items' => $request->boolean('new_items'),
            'wardrobe_tips' => $request->boolean('wardrobe_tips'),
            'weekly_digest' => $request->boolean('weekly_digest'),
            'special_offers' => $request->boolean('special_offers'),
        ];

        $user->update([
            'notification_settings' => json_encode($notificationSettings),
        ]);

        return redirect()->route('profile.edit')
            ->with('success', 'Notification settings updated!')
            ->with('activeTab', 'notifications');
    }

    /**
     * Export user data.
     */
    public function exportData()
    {
        $user = Auth::user();
        
        // Prepare data for export
        $data = [
            'user_info' => [
                'name' => $user->name,
                'email' => $user->email,
                'joined' => $user->created_at->format('Y-m-d H:i:s'),
            ],
            'wardrobe_items' => $user->wardrobes()->count(),
            'favorites' => $user->wardrobes()->where('is_favorite', true)->count(),
            'recommendations' => $user->recommendations()->count(),
        ];

        // In a real app, you might want to generate a JSON/CSV file
        return response()->json($data);
    }
}