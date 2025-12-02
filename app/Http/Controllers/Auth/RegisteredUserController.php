<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\FarmerProfile;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // Determine validation rules based on role
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => 'required|in:farmer,buyer',
            'agree' => 'required|accepted',
        ];

        // Add farmer-specific validation rules
        if ($request->role === 'farmer') {
            $rules = array_merge($rules, [
                'business_name' => 'required|string|max:255',
                'business_permit' => 'required|file|mimes:pdf,png,jpg,jpeg|max:10240',
                'location' => 'required|string|max:255',
            ]);
        }

        $validated = $request->validate($rules);

        // Create the user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'status' => $request->role === 'farmer' ? 'pending' : 'active', // Farmers need approval
        ]);

        // Create farmer profile if registering as farmer
        if ($request->role === 'farmer') {
            $permitPath = null;
            
            // Handle file upload
            if ($request->hasFile('business_permit')) {
                $file = $request->file('business_permit');
                $permitPath = $file->store('business_permits', 'public');
            }
            
            FarmerProfile::create([
                'user_id' => $user->id,
                'business_name' => $request->business_name,
                'business_permit_url' => $permitPath,
                'location' => $request->location,
            ]);
        }

        event(new Registered($user));

        Auth::login($user);

        // Redirect based on role
        if ($user->role === 'farmer') {
            return redirect(route('dashboard', absolute: false))->with('status', 'Your farmer application is pending approval by an admin.');
        }

        return redirect(route('dashboard', absolute: false));
    }
}
