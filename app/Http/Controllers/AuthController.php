<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register', 'verify']]);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()){
            return response()->json($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $token_validity = 60*24;

        $this->guard()->factory()->setTTL($token_validity);

        $credentials = array_merge($validator->validated(), ['is_verified' => 1]);

        if(!$token = $this->guard()->attempt($credentials)){
            return response()->json(['message' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
        }

        return $this->responseWithToken($token);

    }
    
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,20',
            'email' => 'required|email|unique:users',
            'phone' => 'required|numeric',
            'password' => 'required|confirmed|min:6'
        ]);

        if($validator->fails()) {
            return response()->json($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $payloadBuf = [
                'password' => bcrypt($request->password),
                'verification_code' => sha1(time())
            ];

            $payload = array_merge($validator->validated(), $payloadBuf);
            $user = User::createUser($payload);
            if(!empty($user)){
                MailController::sendSignupEmail($user->name, $user->email, $user->verification_code);
            }
            $response = [
                'message' => 'User created succesfully. Please check your email to verification and activation your account',
                'data' => $user
            ];

            return response()->json($response, Response::HTTP_CREATED);

        } catch (QueryException $e) {
            return response()->json(['message' => $e->errorInfo]);
        }
    }

    public function logout(Request $request)
    {
        $this->guard()->logout();
        return response()->json(['message' => 'User logout succesfully'], Response::HTTP_OK);
    }
    
    public function profile(Request $request)
    {
        return response()->json($this->guard()->user());
    }
    
    public function refresh(Request $request)
    {
        return response()->json($this->guard()->refresh());
    }

    public function responseWithToken($token)
    {
        return response()->json([
            'token' => $token,
            'token_type' => 'bearer',
            'token_validity' => $this->guard()->factory()->getTTL() * 60,
            'profile' => $this->guard()->user() 
        ]);
    }

    public function verify(Request $request)
    {
        $verification_code = $request->get('code');

        $user = User::verifyUser($verification_code);

        if(!empty($user)){
            $response = [
                'message' => 'User verification has been successful.',
                'data' => $user
            ];

            return response()->json($response, Response::HTTP_OK);
        } else {
            return response()->json(['message'=>'failed verify user'], Response::HTTP_NOT_ACCEPTABLE);
        }
    }

    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'old_password' => 'required',
            'new_password' => 'required|confirmed|min:6'
        ]);

        if($validator->fails()) {
            return response()->json($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $changePass = User::changePass($validator->validated(), $this->guard()->user()->id);

        return $changePass;
    }
    
    protected function guard()
    {
        return Auth::guard();
    }
}