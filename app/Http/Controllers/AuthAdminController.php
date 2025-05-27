<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthAdminController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:8',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        if (!$token = auth('admins')->attempt($validator->validated())) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }
        return $this->respondWithToken($token);
    }
    public function addAdmin(Request $request)
    {
        $currentAdmin = auth('admins')->user();
        if (!$currentAdmin || !$currentAdmin->is_super_admin) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:admins',
            'password' => 'required|string|min:6',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $admin = Admin::create([
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'password' => Hash::make($request->get('password')),
        ]);
        return response()->json([
            'message' => 'Admin successfully created',
            'admin' => $admin,
        ], 200);
    }
    public function getAccount()
    {
        return response()->json(auth('admins')->user());
    }
    public function logout()
    {
        auth('admins')->logout();
        return response()->json(['message' => 'Successfully Admin logged out']);
    }
    public function refresh()
    {
        return $this->respondWithToken(auth('admins')->refresh());
    }
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('admins')->factory()->getTTL() * 86400,
        ]);
    }
}
