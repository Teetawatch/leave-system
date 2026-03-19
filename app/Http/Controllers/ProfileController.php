<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
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
        $user = $request->user();
        $validated = $request->validated();

        // Handle Avatar Upload
        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($user->avatar) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($user->avatar);
            }
            
            $path = $request->file('avatar')->store('avatars', 'public');
            $validated['avatar'] = $path;
        }

        // Handle Signature Upload
        if ($request->hasFile('signature')) {
            // Delete old signature if exists
            if ($user->signature) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($user->signature);
            }
            
            $path = $request->file('signature')->store('signatures', 'public');
            $validated['signature'] = $path;
        }

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Generate a Telegram link token and return the deep link URL.
     */
    public function generateTelegramLink(Request $request): RedirectResponse
    {
        $user = $request->user();
        $token = Str::random(32);
        $user->update(['telegram_link_token' => $token]);

        $botUsername = env('TELEGRAM_BOT_USERNAME', 'NassLeaveBot');
        $deepLink = "https://t.me/{$botUsername}?start={$token}";

        return Redirect::route('profile.edit')
            ->with('telegram_link', $deepLink)
            ->with('status', 'telegram-link-generated');
    }

    /**
     * Unlink Telegram account.
     */
    public function unlinkTelegram(Request $request): RedirectResponse
    {
        $request->user()->update([
            'telegram_chat_id' => null,
            'telegram_link_token' => null,
        ]);

        return Redirect::route('profile.edit')->with('status', 'telegram-unlinked');
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
