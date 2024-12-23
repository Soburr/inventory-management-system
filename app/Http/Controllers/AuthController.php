<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request) {
         $validator = Validator::make($request->all(), [
            "name" => "required",
            "email" => "required|email",
            "password" => "required",
            "confirm_password" => "required|same:password"
         ]);

         if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "message" => "Validation  Failed",
                "data" => $validator->errors()->all()
            ]);
         }

         $user = User::create([
            "name" => $request->name,
            "email" => $request->email,
            "password" => bcrypt($request->password),
         ]);

         $response = [];
         $response['token'] = $user->createToken("MyApp")->accessToken;
         $response['user'] = $user->name;
         $response['email'] = $user->email;

            return response()->json([
              "status" => 1,
              "message" => "Registration successful",
              "data" => $response
           ]);
    }

    public function login(Request $request) {

       if(Auth::attempt(["email" => $request->email, "password" => $request->password])) {
            $user = Auth::user();

            $response = [];
            $response['token'] = $user->createToken("MyApp")->accessToken;
            $response['user'] = $user->name;
            $response['email'] = $user->email;

            return response()->json([
              "status" => 1,
              "message" => "User Logged-in",
              "data" => $response
           ]);
       }

            return response()->json([
               "status" => 0,
               "message" => "Invalid Credentials",
               "data" => null
            ]);

    }

}
