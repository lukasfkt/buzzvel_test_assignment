<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    /**
     * [POST] - User authentication
     *
     * @param Request request with email and password
     * 
     * @throws Response status code 401 "Unauthorized"
     * @author Lucas Tanaka
     * @return JSON with user token
     */

    public function login(Request $request)
    {
        # Get request information (email and password)
        $credentials = $request->only(['email', 'password']);

        if (!$token = auth()->attempt($credentials)) {
            # Abort with status code 400 with messagem "Unauthorized"
            abort(401, 'Unauthorized');
        }

        return response()->json([
            'data' => [
                'token' => $token,
            ]
        ]);
    }

    /**
     * [POST] - Register new User
     *
     * @param Request request with name, email and password
     * 
     * @throws Response status code 400 with error message
     * @author Lucas Tanaka
     * @return User registered user
     */

    public function register()
    {
        try {
            # Request validation
            $this->validate(request(), [
                'name' => 'required',
                'email' => 'required|email',
                'password' => 'required'
            ]);
        } catch (\Exception $e) {
            # Abort with status code 400 with error message missing some information
            abort(400, $e->getMessage());
        }

        # Create new user
        $user = User::create(request(['name', 'email', 'password']));
        return $user;
    }
}
