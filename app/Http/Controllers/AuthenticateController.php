<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use JWTAuth;
use tymon\jwtauth\src\Exceptions\JWTException;
use App\User;

class AuthenticateController extends Controller
{

public function __construct()
   {
       // Apply the jwt.auth middleware to all methods in this controller
       // except for the authenticate method. We don't want to prevent
       // the user from retrieving their token if they don't already have it
       $this->middleware('jwt.auth', ['except' => ['authenticate']]);
   }

public function index()
{
    // Retrieve all the users in the database and return them
    $users = User::all();
    return $users;
}
public function authenticate(Request $request)
    {
        $credentials = $request->only('email', 'password');
        $user = User::where('email', '=', $credentials['email'])->first();
        
        // $customClaims = ['type' => $user->type];
        try {
            // verify the credentials and create a token for the user
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'invalid_credentials'], 401);
            }
        } catch (JWTException $e) {
            // something went wrong
            return response()->json(['error' => 'could_not_create_token'], 500);
        }

        $response = new \StdClass();

        $response->token = $token;
        $response->type = $user->type;
        // if no errors are encountered we can return a JWT
        return response()->json(compact('response'));
    }
}