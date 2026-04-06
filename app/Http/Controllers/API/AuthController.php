<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\APIService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    protected APIService $apiService;

    public function __construct(APIService $apiService)
    {
        $this->apiService = $apiService;
    }

    public function register(Request $request)
    {
        $request->validate([
            'name'  => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6'
        ]);

        $token = Str::random(60);

        $user = [
            'name' => $request->name,
            'email'=> $request->email,
            'password'=> $request->password,
            'api_token' => $token,
        ];

        $this->apiService->createUser($user);

        return response()->json([
            'message' => 'Registration successful',
            'user'    => $user
        ]);
    }

    public function login(Request $request)
    {
        $response = $this->apiService->checkUser($request); // Http response
        $userData = $response->json()['user'] ?? null; // extract the 'user' array

        if (! $userData || ! Hash::check($request->password, $userData['password'])) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        $token = Str::random(60);

        //Update token
        $this->apiService->updateToken([
            'user_id' => $userData['id'],
            'api_token' => $token
        ]);

        return response()->json([
            'message' => 'Login successful',
            'token' => $token,
            'user'    => [
                'id'    => $userData['id'],
                'name'  => $userData['name'],
                'email' => $userData['email'],
                'roles' => $userData['roles'] ?? [],
                'permissions' => $userData['permissions'] ?? []
            ]
        ]);
    }

    // Logout
    public function logout(Request $request)
    {
        $token = $request->bearerToken();
        $this->apiService->logout($token);

        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }
}
