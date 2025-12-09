<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\LoginRequest;
use App\Http\Requests\Api\Auth\RegisterRequest;
use App\Http\Resources\Api\Auth\LoginResource;
use App\Http\Resources\Api\User\UserResource;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Register a new user and return a JWT token.
     *
     * @param  RegisterRequest  $request
     * @return \Illuminate\Http\JsonResponse|LoginResource
     */
    public function register(RegisterRequest $request)
    {
        // Create the user
        $user = User::create($request->validated());

        $token = auth('api')->login($user);

        return $this->respondWithToken($token);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @param  LoginRequest  $request
     * @return \Illuminate\Http\JsonResponse|LoginResource
     */
    public function login(LoginRequest $request)
    {
        $credentials = $request->only(['email', 'password']);

        if (! $token = auth('api')->attempt($credentials)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized. Invalid credentials.'
            ], 401);
        }

        return $this->respondWithToken($token);
    }

    /**
     * Get the authenticated User.
     *
     * @return LoginResource
     */
    public function me()
    {
        return UserResource::make(auth('api')->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth('api')->logout();

        return response()->json([
            'status' => 'success',
            'message' => 'Successfully logged out'
        ]);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse|LoginResource
     */
    public function refresh()
    {
        return $this->respondWithToken(auth('api')->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     * @return LoginResource
     */
    protected function respondWithToken($token)
    {
        $user = auth('api')->user();
        $user->token = $token;

        return LoginResource::make($user);
    }
}
