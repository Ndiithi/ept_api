<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Create User
     * @param Request $request
     * @return User 
     */
    public function createUser(Request $request)
    {
        try {

            $validateUser = Validator::make(
                $request->all(),
                [
                    // 'uuid' => 'nullable',
                    'name' => 'required',
                    'email' => 'required|email|unique:users,email',
                    'password' => 'required',
                    // 'role' => 'required'
                ]
            );

            if ($validateUser->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $validateUser->errors()
                ], 401);
            }
            $guestRole = Role::where('name', 'like', '%guest%')->first();
            if (!$guestRole) {
                Log::error('Guest Role not found');
                return response()->json([
                    'status' => false,
                    'message' => 'An error occured, please try again later'
                ], 401);
            }
            $user = User::create([
                'uuid' => Str::uuid(),
                'name' => $request->name,
                'email' => $request->email,
                // 'role' => Role::where('name', 'like', '%guest%')->first()->uuid ?? 2,         //$request->role,
                'role' => $guestRole->uuid,
                'password' => Hash::make($request->password),
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Signup successful',
                'token' => $user->createToken("API TOKEN")->plainTextToken
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Add User
     * @param Request $request
     * @return User 
     */
    public function addUser(Request $request)
    {
        try {

            $validateUser = Validator::make(
                $request->all(),
                [
                    // 'uuid' => 'nullable',
                    'name' => 'required',
                    'email' => 'required|email|unique:users,email',
                    'password' => 'required',
                    'role' => 'required'
                ]
            );

            if ($validateUser->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $validateUser->errors()
                ], 401);
            }
            $user = User::create([
                'uuid' => Str::uuid(),
                'name' => $request->name,
                'email' => $request->email,
                'role' => Role::find($request->role)->uuid ?? Role::where('name', 'like', '%guest%')->first()->uuid,         //$request->role,
                'password' => Hash::make($request->password),
            ]);

            return response()->json([
                'status' => true,
                'message' => 'User Created Successfully',
                'data' => $user,
                'token' => $user->createToken("API TOKEN")->plainTextToken
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Login The User
     * @param Request $request
     * @return User
     */
    public function loginUser(Request $request)
    {
        try {
            $validateUser = Validator::make(
                $request->all(),
                [
                    'email' => 'required|email',
                    'password' => 'required'
                ]
            );

            if ($validateUser->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $validateUser->errors()
                ], 401);
            }

            if (!Auth::attempt($request->only(['email', 'password']))) {
                return response()->json([
                    'status' => false,
                    'message' => 'Email & Password does not match with our record.',
                ], 401);
            }

            $user = User::where('email', $request->email)->first();

            return response()->json([
                'status' => true,
                'message' => 'User Logged In Successfully',
                'token' => $user->createToken("API TOKEN")->plainTextToken
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }


    /**
     * Logout The User
     * @param Request $request
     * @return null
     */
    public function logoutUser(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'status' => true,
                'message' => 'User Logged Out Successfully',
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }


    /**
     * Get User Details
     * @param Request $request
     * @return User
     */
    public function getUser(Request $request)
    {
        try {
            $user = $request->user();
            $user_programs = $user->programs();
            return response()->json([
                'status' => true,
                'message' => 'User Details',
                'user' => $request->user(),
                'role' => Role::where('uuid',$user->role)->first(),
                'permissions' => $user->permissions(),
                'programs' => $user_programs,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }


    /**
     * Update User Details
     * @param Request $request
     * @return User
     */
    public function updateUser(Request $request)
    {
        try {
            $validateUser = Validator::make(
                $request->all(),
                [
                    'name' => 'required',
                    'email' => 'required|email|unique:users,email,' . $request->user()->id,
                    'role' => 'required'
                ]
            );

            if ($validateUser->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $validateUser->errors()
                ], 401);
            }

            $user = $request->user();
            if ($request->has('name')) {
                $user->name = $request->name;
            }
            if ($request->has('email')) {
                $user->email = $request->email;
            }
            if ($request->has('role')) {
                $user->role = Role::find($request->role)->uuid ?? Role::where('name', 'like', '%guest%')->first()->uuid;
                // if role does not exist then throw error
                if (!$user->role) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Role does not exist',
                    ], 401);
                }
            }
            $user->save();

            return response()->json([
                'status' => true,
                'message' => 'User Details Updated Successfully',
                'user' => $user,
                'role' => Role::where('uuid',$user->role)->first(),
                'permissions' => $user->permissions(),
                'programs' => $user->programs(),
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }
}
