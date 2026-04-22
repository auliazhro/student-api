<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // POST /api/register
    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string',
            'email'    => 'required|email|unique:users',
            'password' => 'required|min:6',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = Auth::guard('api')->login($user);

        return response()->json([
            'message' => 'User registered successfully',
            'token'   => $token,
            'user'    => $user,
        ], 201);
    }

    // POST /api/login
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');

        if (!$token = Auth::guard('api')->attempt($credentials)) {
            return response()->json([
                'message' => 'Email or password is incorrect'
            ], 401);
        }

        return response()->json([
            'message' => 'Login successful',
            'token'   => $token,
        ]);
    }

    // POST /api/logout
    public function logout()
    {
        Auth::guard('api')->logout();
        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }

    // POST /api/refresh
    public function refresh()
    {
        $token = Auth::guard('api')->refresh();
        return response()->json([
            'token' => $token
        ]);
    }

    // GET /api/me
    public function me()
    {
        return response()->json(Auth::guard('api')->user());
    }
}
