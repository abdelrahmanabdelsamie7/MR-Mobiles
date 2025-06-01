<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
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
    public function logout()
    {
        auth('admins')->logout();
        return response()->json(['message' => 'Successfully Admin logged out']);
    }
    public function getAccount()
    {
        return response()->json(auth('admins')->user());
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