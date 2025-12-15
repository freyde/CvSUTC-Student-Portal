<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the student's profile form.
     */
    public function edit(Request $request): View
    {
        abort_unless(Auth::check() && Auth::user()->isStudent(), 403);

        $user = $request->user()->load('program');

        return view('student.profile.edit', compact('user'));
    }

    /**
     * Update the student's password.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        abort_unless(Auth::check() && Auth::user()->isStudent(), 403);

        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('status', 'password-updated');
    }
}

