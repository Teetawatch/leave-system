<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Str;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): Response
    {
        $user = $request->user();
        return Inertia::render('Profile/Edit', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'rank' => $user->rank,
                'role' => $user->role,
                'department' => $user->department,
                'avatar' => $user->avatar,
                'signature' => $user->signature,
                'phone' => $user->phone ?? '',
                'telegram_chat_id' => $user->telegram_chat_id,
            ],
            'status' => session('status'),
            'telegram_link' => session('telegram_link'),
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
        \Illuminate\Support\Facades\Log::info('unlinkTelegram called for user: ' . $request->user()->id);
        
        $user = $request->user();
        $user->telegram_chat_id = null;
        $user->telegram_link_token = null;
        $user->save();

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
