<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'subdomain' => [
                'required',
                'string',
                'max:50',
                'regex:/^[a-z0-9-]+$/',
                'unique:'.User::class,
                function ($attribute, $value, $fail) {
                    $reserved = ['www', 'admin', 'api', 'app', 'mail', 'ftp', 'localhost', 'dashboard'];
                    if (in_array(strtolower($value), $reserved)) {
                        $fail('This subdomain is reserved and cannot be used.');
                    }
                },
            ],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'subdomain' => strtolower($request->subdomain),
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'subscription_status' => 'incomplete',
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false))
            ->with('status', 'Registration complete! Please set up billing to activate your account.');
    }
}
