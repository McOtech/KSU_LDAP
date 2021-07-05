<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Contracts\Session\Session;
use Illuminate\Support\Facades\Auth;
use App\Http\Traits\Utils;


class AuthController extends Controller
{
    use Utils;
    public function register(Request $request)
    {
        try {
            $credentials = $request->validate([
                'username' => ['required', 'string', 'max:100'],
                'password' => ['required', 'string', 'confirmed']
            ]);
        } catch (\Throwable $th) {
            return response()->json($this->alert(env('ERROR_MESSAGE'), $th->getMessage()));
        }
    }

    public function logout(Request $request)
    {
        if ($request->session()->has('user')) {
            auth()->user()->tokens()->delete();
            $request->session()->flush();
            return response(["message" => 'User logged out succesfully'], 200);
        } else {
            return response(["message" => 'User not logged in'], 200);
        }
    }

    public function login(Request $request)
    {

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