<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\User;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    /**
     * Create user
     *
     * @param  [string] name
     * @param  [string] email
     * @param  [string] password
     * @return [string] message
     */
    public function signup(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string'
        ]);
        $user = new User([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);
        $user->save();
        return response()->json([
            'message' => 'Successfully created user!'
        ], 201);
    }

    /**
     * Login user and create token
     *
     * @param  [string] email
     * @param  [string] password
     * @return [string] access_token
     * @return [string] user
     * @return [string] token_type
     * @return [string] expires_at
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);
        $credentials = request(['email', 'password']);
        if (!Auth::attempt($credentials))
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);
        $user = $request->user();
        $cur_user = Auth::user();
        $tokenResult = $user->createToken('Secret User');
        $token = $tokenResult->token;
        $token->save();

        return response()->json([
            'id_token' => $token->id,
            'access_token' => $tokenResult->accessToken,
            'user' => $cur_user,
            'token_type' => 'Bearer',
            'expires_at' => Carbon::parse(
                $tokenResult->token->expires_at
            )->toDateTimeString(),
        ]);
//        return $tokenResult->toArray();
    }

    /**
     * Check token expired
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function check(Request $request)
    {
        $token = DB::table('oauth_access_tokens')->where('id', '=', $request->id)->first();
        $current_time = Carbon::now();

        if ($current_time->greaterThanOrEqualTo(Carbon::parse($token->expires_at))) {
            DB::table('oauth_access_tokens')->where('id', '=', $request->id)->update([
                'revoked' => 1,
                'updated_at' => Carbon::now(),
            ]);
            return response()->json([
                'message' => 'token has expired',
                'exp' => false,
            ]);
        }

       return response()->json([
            'message' => 'token is still valid',
            'exp' => true,
       ]);
    }

    /**
     * Logout user (Revoke the token)
     *
     * @return [string] message
     */
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }

    /**
     * Get the authenticated User
     *
     * @return [json] user object
     */
    public function user(Request $request)
    {
        return response()->json($request->user());
    }
}
