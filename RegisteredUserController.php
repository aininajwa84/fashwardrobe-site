<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
{
    $request->validate([
        'full_name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
        'password' => ['required', 'confirmed', Rules\Password::defaults()],
    ]);

    // Generate username dari email
    $baseUsername = strtok($request->email, '@'); // ambil sebelum @
    $username = $baseUsername . rand(100, 999); // tambah nombor rawak

    // Check jika username sudah wujud
    while (User::where('username', $username)->exists()) {
        $username = $baseUsername . rand(1000, 9999);
    }

    // DEBUG: Check apa yang di-pass
    \Log::info('Registration data:', [
        'full_name' => $request->full_name,
        'username' => $username,
        'email' => $request->email
    ]);

    // Create user - PASTIKAN FULL_NAME ADA
    $userData = [
        'username' => $username,
        'full_name' => $request->full_name, // ✅ INI MESTI ADA
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'phone' => null,
        'theme_preferences' => json_encode(['Casual', 'Minimalist']),
    ];

    \Log::info('User data to insert:', $userData);

    $user = User::create($userData);

    event(new Registered($user));
    Auth::login($user);

    return redirect(RouteServiceProvider::HOME);
}
}