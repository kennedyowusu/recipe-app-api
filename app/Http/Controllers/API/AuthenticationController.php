<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\UserRequest;
use App\Models\User;

class AuthenticationController extends Controller
{
    public function register(UserRequest $request){

        $validatedData = $request->validated();

        $validator = Validator::make($validatedData, [
            'password' => ['required', 'string', 'min:8'],
        ], [
            'password.min' => 'Password must be at least 8 characters long',
        ]);

        if($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()->first(),
            ], 422);
        }

        $newUser = new User([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password'])
        ]);

        $newUser->save();

        $newUserToken = $newUser->createToken('authToken')->plainTextToken;

        return response()->json([
            'message' => 'User created successfully',
            'user' => $newUser,
            'token' => $newUserToken
        ], 201);
    }

    public function login(Request $request) {
        $loginData = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ], [
            'email.required' => 'Email Address is required',
            'email.email' => 'Email Address is invalid',
            'password.required' => 'Password is required'
        ]);

        if(!auth()->attempt($loginData)) {
            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        }

        $user = User::where('email', $loginData['email'])->firstOrFail();

        $userToken = $user->createToken('authToken')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'user' => $user,
            'token' => $userToken
        ], 200);
    }

    public function logout(Request $request) {
        auth()->user()->tokens()->delete();

        return response()->json([
            'message' => 'Logout successful'
        ], 200);
    }
}
