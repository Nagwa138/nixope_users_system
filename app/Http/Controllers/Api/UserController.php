<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserCreateRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserController extends BasicController
{

    public function index()
    {
        if(auth()->user()->hasRole('admin'))
        {
            $users = User::where('id', '!=', auth()->id())->paginate(10);
            return $this->sendResponse(UserResource::collection($users), 'User list success!');
        } else {
            return $this->sendError('This action is unauthorized', [], Response::HTTP_UNAUTHORIZED);
        }
    }

    /**
     * @param UserCreateRequest $request
     * @return JsonResponse
     */
    public function store(UserCreateRequest $request): JsonResponse
    {
        try {
            $input = $request->except('confirm_password', 'roles');
            $input['password'] = bcrypt($input['password']);
            $user = User::create($input);
            $user->attachRoles($request['roles']);
            return $this->sendResponse(new UserResource($user), 'User created success!');
        } catch (\Throwable $exception) {
            return $this->sendError('Error occurred', ['error' => $exception->getMessage()],Response::HTTP_NOT_ACCEPTABLE);
        }
    }

    /**
     * @param User $user
     * @return JsonResponse
     */
    public function show(User $user): JsonResponse
    {
        return $this->sendResponse(new UserResource($user), 'User created success!');
    }


    public function update(UserUpdateRequest $request, User $user): JsonResponse
    {
        try {
            $input = $request->except('confirm_password', 'roles');
            if ($request->has('password'))
            {
                $input['password'] = bcrypt($input['password']);
            }
            $user->update($input);
            if ($request->has('roles'))
            {
                $user->syncRoles($request['roles']);
            }
            return $this->sendResponse(new UserResource($user), 'User updated success!');
        } catch (\Throwable $exception) {
            return $this->sendError('Error occurred', ['error' => $exception->getMessage()],true);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param User $user
     * @return JsonResponse
     */
    public function destroy(User $user): JsonResponse
    {
        if(auth()->user()->hasRole('admin'))
        {
            $user->delete();
            return $this->sendResponse('', 'User deleted success!');
        } else {
            return $this->sendError('This action is unauthorized', [], Response::HTTP_UNAUTHORIZED);
        }
    }
}
