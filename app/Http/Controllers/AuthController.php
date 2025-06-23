<?php

namespace App\Http\Controllers;

use App\Http\Requests\ApiLoginRequest;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;



//Standard Json Response template:


// {
//   "success": true,
//   "message": "Login successful.",
//   "data": {
//     "user": {
//       "id": 1,
//       "name": "Adnan",
//       "email": "adnan@example.com"
//     },
//     "token": "eyJ0eXAiOiJKV1QiLCJhbGci..."
//   }
// }



//Success Response Example:

// return response()->json([
//     'success' => true,
//     'message' => 'Login successful.',
//     'data' => [
//         'user' => $user,
//         'token' => $token,
//     ]
// ], 200);


// Error Response Example:

// return response()->json([
//     'success' => false,
//     'message' => 'Invalid credentials.',
//     'errors' => [
//         'email' => ['These credentials do not match our records.']
//     ]
// ], 401);





class AuthController extends Controller
{

  public function register(Request $request): JsonResponse
{
    $validated = $request->validate([
        'email' => 'required|email|unique:users,email',
        'password' => 'required|string|min:6',
    ]);

    $user = User::create([
        'name' => $validated['email'], // You can customize this
        'email' => $validated['email'],
        'password' => $validated['password'], // Automatically hashed because of casts
    ]);

    $token = $user->createToken('api-token')->plainTextToken;

    return response()->json([
        'success' => true,
        'message' => 'Registration successful.',
        'data' => [
            'user' => [
                'id' => $user->id,
                'email' => $user->email,
            ],
            'token' => $token,
        ],
    ], 201);
}
    public function login(ApiLoginRequest $request): JsonResponse
    {
        $credentials = $request->only('email', 'password');

        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials.',
                'errors' => [
                    'email' => ['These credentials do not match our records.']
                ]
            ], 401);
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login successful.',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ],
                'token' => $token
            ]
        ], 200);
    }




    public function logout(Request $request)
{
    // Revoke the current access token
    $request->user()->currentAccessToken()->delete();

    return response()->json([
        'success' => true,
        'message' => 'Logged out successfully.',
    ]);
}
}
