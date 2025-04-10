<?php
namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Hash, Validator,Mail};
use Illuminate\Support\Str;
use App\Mail\{VerifyEmail, ResetPassword};
class AuthUserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register', 'verifyEmail', 'resendVerification', 'forgotPassword', 'resetPassword']]);
    }
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }
        if (!$user->email_verified_at) {
            return response()->json(['error' => 'Please verify your email before logging in.'], 403);
        }
        $token = auth('api')->login($user);
        return response()->json([
            'message' => 'تم تسجيل الدخول بنجاح!',
            'access_token' => $token,
            'user' => $user
        ]);
    }
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'phone_number' => 'required|string|min:8|max:15|regex:/^[0-9]+$/',
            'country' => 'required|string|max:50',
            'city' => 'required|string|max:50',
            'street' => 'required|string|max:100',
            'apartment' => 'nullable|string|max:10',
            'floor' => 'nullable|string|max:10',
            'building' => 'nullable|string|max:10',
            'postal_code' => 'nullable|string|max:20',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }
        $verificationToken = bin2hex(random_bytes(30));
        $user = User::create([
            'email' => $request->email,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'phone_number' => $request->phone_number,
            'country' => $request->country,
            'city' => $request->city,
            'street' => $request->street,
            'apartment' => $request->apartment,
            'floor' => $request->floor,
            'building' => $request->building,
            'postal_code' => $request->postal_code,
            'password' => Hash::make($request->get('password')),
            'verification_token' => $verificationToken,
            'verification_token_expires_at' => now()->addHours(3),
        ]);
        Mail::to($user->email)->queue(new VerifyEmail($user));
        return response()->json([
            'message' => 'User registered successfully. Please check your email to verify your account.',
        ], 201);
    }
    public function verifyEmail($token)
    {
        if (empty($token)) {
            return response()->json(['error' => 'Verification token is required'], 400);
        }
        $user = User::where('verification_token', $token)
            ->whereNull('email_verified_at')
            ->where('verification_token_expires_at', '>', now())
            ->first();
        if (!$user) {
            return response()->json(['error' => 'Invalid or expired verification token'], 400);
        }
        $user->email_verified_at = now();
        $user->verification_token = null;
        $user->verification_token_expires_at = null;
        if (!$user->save()) {
            return response()->json(['error' => 'Failed to verify email. Please try again.'], 500);
        }
        return response()->json([
            'message' => 'Email verified successfully. You can now log in.',
            'user' => [
                'id' => $user->id,
                'name' => $user->first_name ." ". $user->last_name,
                'email' => $user->email,
                'email_verified_at' => $user->email_verified_at
            ]
        ], 200);
    }
    public function resendVerification(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|exists:users,email'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $user = User::where('email', $request->email)->first();
        if ($user->email_verified_at) {
            return response()->json(['message' => 'Email is already verified.'], 200);
        }
        $user->verification_token = Str::random(60);
        $user->verification_token_expires_at = now()->addHours(3);
        $user->save();
        Mail::to($user->email)->queue(new VerifyEmail($user));
        return response()->json(['message' => 'Verification email has been resent.']);
    }
    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|exists:users,email',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $user = User::where('email', $request->email)->first();
        $token = Str::random(60);
        $user->reset_token = $token;
        $user->reset_token_expires_at = now()->addHour();
        $user->save();
        try {
            Mail::to($user->email)->queue(new ResetPassword($user, $token));
        } catch (\Exception $e) {
            \Log::error('Password reset email failed: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to send reset email.'], 500);
        }
        return response()->json(['message' => 'Password reset link sent to your email.'], 200);
    }
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|exists:users,email',
            'token' => 'required|string',
            'password' => 'required|string|confirmed|min:8',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $user = User::where('email', $request->email)
            ->where('reset_token', $request->token)
            ->where('reset_token_expires_at', '>', now())
            ->first();
        if (!$user) {
            return response()->json(['error' => 'Invalid or expired reset token.'], 400);
        }
        $user->password = Hash::make($request->password);
        $user->reset_token = null;
        $user->reset_token_expires_at = null;
        $user->save();
        return response()->json(['message' => 'Password reset successfully.'], 200);
    }
    public function getaccount()
    {
        return response()->json(auth('api')->user());
    }
    public function logout()
    {
        auth('api')->logout();
        return response()->json(['message' => 'Successfully logged out']);
    }
    public function deleteAccount(Request $request)
    {
        $user = auth('api')->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        auth('api')->logout();
        $user->delete();
        return response()->json(['message' => 'Account deleted successfully.'], 200);
    }
    public function refresh()
    {
        return $this->respondWithToken(auth('api')->refresh());
    }
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 120
        ]);
    }
}