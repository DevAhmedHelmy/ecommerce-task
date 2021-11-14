<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ApiAuthController extends Controller
{
    public function register(Request $request)
    {

        $validatedData = $request->validate([
            'name' => 'required|max:55',
            'email' => 'email|required|unique:users',
            'password' => 'required|confirmed'
        ]);

        $validatedData['password'] = bcrypt($request->password);

        $user = User::create($validatedData);



        return response()->json(['data' => $user, 'access_token' => $user->createToken('access_token')->plainTextToken], 200);
    }
    public function login(Request $request)
    {
        $loginData = $request->validate([
            'email' => 'email|required',
            'password' => 'required'
        ]);
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {

            $user = Auth::user();
            $user->tokens()->delete();

            return [
                'data' => $user,
                'access_token' => $user->createToken('access_token')->plainTextToken,
            ];
        }
        return response()->json(['message' => 'The Email or The Password Wrong'], 401);
    }
    // public function login(Request $request)
    // {


    //     $loginData = $request->validate([
    //         'email' => 'email|required',
    //         'password' => 'required'
    //     ]);
    //     if (!auth()->attempt([
    //         'email' => $request->email,
    //         'password' => $request->password
    //     ])) {
    //         return response()->json(['message' => 'The Email or The Password Wrong'], 401);
    //     }

    //     $accessToken = auth()->user()->createToken('authToken')->accessToken;
    //     return response()->json(['user' => auth()->user(), 'access_token' => $accessToken->token], 200);
    // }
}
