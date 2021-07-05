<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Contracts\Session\Session;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function Register(Request $request) {
        $fields = [];
        $imageUrl = '';

        try {
            $fields = $request->validate([
                'fname' => ['required', 'string'],
                'lname' => ['required', 'string'],
                'phone' => ['required', 'string'],
                'image' => ['required', 'image'],
                'postal_code' => ['required', 'string'],
                'postal_address' => ['required', 'string'],
                'town' => ['required', 'string'],
                'type' => ['required', 'string', 'unique:profiles,type'],
                'username' => ['required', 'string', 'unique:users,name'],
                'email' => ['required', 'email', 'unique:users,email'],
                'password' => ['required', 'string', 'confirmed'],
            ]);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()], 400);
        }

        try {
            $imageUrl = $request->file('image')->store('public/profiles');
            if ($imageUrl) {
                $fields['image'] = $imageUrl;
            } else {
                return response()->json(['message' => 'Image upload failed.'], 400);
            }
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()], 400);
        }

        try {
            $user = User::create([
                'name' => $fields['username'],
                'email' => $fields['email'],
                'password' => bcrypt($fields['password']),
            ]);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Error occured while creating the user. Please try again later'], 400);
        } finally {
            try {
                $profile = Profile::create([
                    'user_id' => $user['id'],
                    'fname' => $fields['fname'],
                    'lname' => $fields['lname'],
                    'phone' => $fields['phone'],
                    'image' => $imageUrl,
                    'postal_address' => $fields['postal_address'],
                    'postal_code' => $fields['postal_code'],
                    'town' => $fields['town'],
                    'type' => $fields['type']
                ]);

                $token = $user->createToken('auth')->plainTextToken;
                $response = [
                    'user' => [
                        'id' => $user['id'],
                        'name' => $user['name'],
                        'email' => $user['email']
                    ],
                    'token' => $token
                ];
                // $request->headers->set('Authorization', 'Bearer '.$token);
                $request->session()->put('user', [
                    'id' => $user['id'],
                    'name' => $user['name'],
                    'email' => $user['email']
                ]);
                return response($response, 201);
            } catch (\Throwable $th) {
                return response()->json(['message' => $th->getMessage()], 400);
            }
        }
    }

    public function logout(Request $request) {
        if ($request->session()->has('user')) {
            auth()->user()->tokens()->delete();
            $request->session()->flush();
            return response(["message" => 'User logged out succesfully'], 200);
        } else {
            return response(["message" => 'User not logged in'], 200);
        }

    }

    public function login(Request $request) {

        try {
            if ($request->session()->has('user')) {
                return response()->json([
                    'message' => 'You are logged in. Please log out to proceed.'
                ], 400);
            }

            $fields = $request->validate([
                'email' => ['required', 'email'],
                'password' => ['required', 'string'],
            ]);

            // Check email
            $user = User::where('email', $fields['email'])->first();

            // Check password
            if (!$user || !Hash::check($fields['password'], $user->password)) {
                return response()->json([
                    'message' => 'Bad Credentials'
                ], 401);
            }

            // Create User token
            $token = $user->createToken('auth')->plainTextToken;
            $response = [
                'user' => [
                    'id' => $user['id'],
                    'name' => $user['name'],
                    'email' => $user['email']
                ],
                'token' => $token
            ];

            // Set User Session
            $request->session()->put('user', [
                'id' => $user['id'],
                'name' => $user['name'],
                'email' => $user['email']
            ]);
            return response($response, 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()], 400);
        }
    }
}