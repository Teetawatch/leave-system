<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Login and get API token
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'device_name' => 'nullable|string',
        ]);

        $user = \App\Models\User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['ข้อมูลการเข้าสู่ระบบไม่ถูกต้อง'],
            ]);
        }

        // Check if user can login
        if (!$user->canLogin()) {
            throw ValidationException::withMessages([
                'email' => ['บัญชีของคุณยังไม่ได้รับการอนุมัติ'],
            ]);
        }

        // Revoke old tokens (optional - keep only one active session)
        // $user->tokens()->delete();

        $deviceName = $request->device_name ?? 'mobile_app';
        $token = $user->createToken($deviceName)->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'เข้าสู่ระบบสำเร็จ',
            'data' => [
                'user' => new UserResource($user->load(['supervisor', 'manager'])),
                'token' => $token,
                'token_type' => 'Bearer',
            ],
        ]);
    }

    /**
     * Logout and revoke token
     */
    public function logout(Request $request)
    {
        // Revoke the current access token
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'ออกจากระบบสำเร็จ',
        ]);
    }

    /**
     * Get authenticated user profile
     */
    public function me(Request $request)
    {
        $user = $request->user()->load(['supervisor', 'manager']);

        return response()->json([
            'success' => true,
            'data' => new UserResource($user),
        ]);
    }

    /**
     * Update user profile (avatar, signature)
     */
    public function updateProfile(Request $request)
    {
        $request->validate([
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'signature' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $user = $request->user();

        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $path;
        }

        if ($request->hasFile('signature')) {
            $path = $request->file('signature')->store('signatures', 'public');
            $user->signature = $path;
        }

        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'อัปเดตข้อมูลสำเร็จ',
            'data' => new UserResource($user->load(['supervisor', 'manager'])),
        ]);
    }

    /**
     * Update FCM Token
     */
    public function updateFcmToken(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
        ]);

        $user = $request->user();
        $user->fcm_token = $request->token;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'FCM Token updated',
        ]);
    }
}
