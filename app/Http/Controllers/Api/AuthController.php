<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AuthController extends BasicController
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function signin(Request $request): JsonResponse
    {
        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){
            $authUser = Auth::user();
            $success['token'] =  $authUser->createToken('MyAuthApp')->plainTextToken;
            $success['name'] =  $authUser->name;

            return $this->sendResponse($success, 'User signed in');
        }
        else{
            return $this->sendError('Unauthorised.', ['error'=>'Unauthorised']);
        }
    }

    /**
     */
    public function signup(Request $request): JsonResponse
    {
        $validator =Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'confirm_password' => 'required|same:password'
        ]);

        if($validator->fails()){
            return $this->sendError('Error validation', json_decode(($validator->errors())->toJson() ,true));
        }

        $input = $request->except('confirm_password');
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $success['token'] =  $user->createToken('MyAuthApp')->plainTextToken;
        $success['name'] =  $user->name;

        return $this->sendResponse($success, 'User created successfully.');
    }

    public function logout(): JsonResponse
    {
        if(auth()->check())
        {
            auth()->user()->tokens()->delete();
        }
        return $this->sendResponse([], 'User Logout successfully.');
    }

}
