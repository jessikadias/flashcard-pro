<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Generate a new API token for the user.
     */
    public function generateApiToken(Request $request): RedirectResponse
    {
        $user = $request->user();
        
        // Clear any existing cached token before generating a new one
        if ($user->api_token) {
            Cache::forget("api_token:{$user->api_token}");
        }
        
        $token = $user->generateApiToken();

        return Redirect::to(route('profile.edit') . '#api-token')->with([
            'status' => 'api-token-generated',
            'api_token' => $token
        ]);
    }

    /**
     * Revoke the user's API token.
     */
    public function revokeApiToken(Request $request): RedirectResponse
    {
        $user = $request->user();
        
        // Clear the cached token before revoking it
        if ($user->api_token) {
            Cache::forget("api_token:{$user->api_token}");
        }
        
        $user->revokeApiToken();

        return Redirect::to(route('profile.edit') . '#api-token')->with('status', 'api-token-revoked');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
