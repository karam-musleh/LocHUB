<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Requests\LoginRequest;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\RegisterRequest;
use Tymon\JWTAuth\Exceptions\JWTException;
use OpenApi\Attributes as OA;



class RegisterController extends Controller
{

    use ApiResponseTrait;
#[OA\Post(
    path: "/api/register",
    summary: "Register a new user",
    tags: ["Auth"],
    requestBody: new OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ["name","email","password"],
            properties: [
                new OA\Property(property: "name", type: "string", example: "John Doe"),
                new OA\Property(property: "email", type: "string", example: "test@test.com"),
                new OA\Property(property: "password", type: "string", example: "123456")
            ]
        )
    ),
    responses: [
        new OA\Response(response: 201, description: "User registered successfully"),
        new OA\Response(response: 400, description: "Validation error")
    ]
)]
    public function register(RegisterRequest $request)
    {
        $user = User::create([
            'name'       => $request->name,
            'email'      => $request->email,
            'phone'      => $request->phone,
            'password'   => Hash::make($request->password),
            'role'       => $request->role,
            'location_id' => $request->location_id,
            'specialization' => $request->specialization,
        ]);
        $token = Auth::guard('api')->login($user);
        return $this->successResponse(['user' => new UserResource($user), 'token' => $token], 'User registered successfully', 201);
    }

#[OA\Post(
    path: "/login",
    summary: "User login",
    requestBody: new OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ["email","password"],
            properties: [
                new OA\Property(property: "email", type: "string", example: "test@test.com"),
                new OA\Property(property: "password", type: "string", example: "123456")
            ]
        )
    ),
    responses: [
        new OA\Response(response: 200, description: "Login successful"),
        new OA\Response(response: 401, description: "Unauthorized")
    ]
)]
    public function login(LoginRequest $request)
    {
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return $this->errorResponse('User not found', 401);
        }
        if (!Hash::check($request->password, $user->password)) {
            return $this->errorResponse('Invalid password', 400);
        }

        $token = Auth::guard('api')->login($user);
        return $this->successResponse(
            [
                new UserResource($user),
                'token' => $token
            ],
            'User logged in successfully',
            200,

        );
    }

    #[OA\Post(
    path: "/logout",
    summary: "User logout",
    requestBody: new OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ["email","password"],
            properties: [
                new OA\Property(property: "email", type: "string", example: "test@test.com"),
                new OA\Property(property: "password", type: "string", example: "123456")
            ]
        )
    ),
    responses: [
        new OA\Response(response: 200, description: "Login successful"),
        new OA\Response(response: 401, description: "Unauthorized")
    ]
)]
    public function logout()
    {
        try {
            auth()->guard('api')->logout();
            return $this->successResponse(null, 'User logged out successfully', 200);
        } catch (JWTException $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    #[OA\Post(
    path: "/refresh",
    summary: "User refresh token",
    requestBody: new OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ["email","password"],
            properties: [
                new OA\Property(property: "email", type: "string", example: "test@test.com"),
                new OA\Property(property: "password", type: "string", example: "123456")
            ]
        )
    ),
    responses: [
        new OA\Response(response: 200, description: "Login successful"),
        new OA\Response(response: 401, description: "Unauthorized")
    ]
)]
    public function refresh()
    {
        try {
            $newToken = JWTAuth::parseToken()->refresh();
            return response()->json([
                'status' => 'success',
                'token' => $newToken
            ], 200);
        } catch (JWTException $e) {
            return $this->errorResponse('Could not refresh token', 401);
        }
    }
}
