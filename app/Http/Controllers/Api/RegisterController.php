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
use OpenApi\Annotations as OA;


/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="API Documentation",
 *     description="Example API documentation"
 * )
 */

class RegisterController extends Controller
{

    use ApiResponseTrait;
    //
    /**
 * @OA\Post(
 *     path="/api/register",
 *     tags={"Auth"},
 *     summary="Register new user",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"name","email","password"},
 *             @OA\Property(property="name", type="string", example="Ahmad"),
 *             @OA\Property(property="email", type="string", example="ahmad@mail.com"),
 *             @OA\Property(property="phone", type="string", example="0599999999"),
 *             @OA\Property(property="password", type="string", example="12345678"),
 *             @OA\Property(property="role", type="string", example="user"),
 *             @OA\Property(property="location_id", type="integer", example=1),
 *             @OA\Property(property="specialization", type="string", example="plumber")
 *         )
 *     ),
 *     @OA\Response(response=201, description="User registered successfully")
 * )
 */

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

    /**
 * @OA\Post(
 *     path="/api/login",
 *     tags={"Auth"},
 *     summary="User login",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"email","password"},
 *             @OA\Property(property="email", type="string", example="ahmad@mail.com"),
 *             @OA\Property(property="password", type="string", example="12345678")
 *         )
 *     ),
 *     @OA\Response(response=200, description="Login success"),
 *     @OA\Response(response=401, description="Invalid credentials")
 * )
 */
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

    /**
 * @OA\Post(
 *     path="/api/logout",
 *     tags={"Auth"},
 *     summary="Logout user",
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(response=200, description="Logged out successfully")
 * )
 */
    public function logout()
    {
        try {
            auth()->guard('api')->logout();
            return $this->successResponse(null, 'User logged out successfully', 200);
        } catch (JWTException $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
    /**
 * @OA\Post(
 *     path="/api/refresh",
 *     tags={"Auth"},
 *     summary="Refresh JWT token",
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(response=200, description="Token refreshed")
 * )
 */
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
