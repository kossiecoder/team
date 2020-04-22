<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UserCreateReqeust;
use App\Http\Requests\User\UserDeleteRequest;
use App\Http\Requests\User\UserUpdateRequest;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(User $user)
    {
        $users = $user->withCount(['fromMessages' => function($query) {
            $query->where('is_read', 0)->where('to', auth()->id());
        }])->get();

        return response()->json([
            'users' => $users
        ], Response::HTTP_OK);
    }

    public function store(UserCreateReqeust $request)
    {
        $user = User::create([
            'email' => $request['email'],
            'first_name' => $request['firstName'],
            'last_name' => $request['lastName'],
            'password' => Hash::make($request['password'])
        ]);

        return response()->json([
            'user' => $user
        ], Response::HTTP_CREATED);
    }

    public function update(UserUpdateRequest $request, User $user)
    {
        $user->update([
            'email' => $request['email'],
            'first_name' => $request['first_name'],
            'last_name' => $request['last_name'],
            'password' => Hash::make($request['password'])
        ]);
//        $user->notify(new PasswordResetSuccess($passwordReset));
        return response()->json([
            'user' => $user
        ], Response::HTTP_OK);
    }

    public function destroy(UserDeleteRequest $request, User $user)
    {
        $user->delete();
        return response()->json([
            'user' => $user
        ], Response::HTTP_OK);
    }

    public function me()
    {
        $user = request()->user()->load(['notifications' => function($query) {
            $query->latest()->take(15);
        }])->loadCount('unreadNotifications');
        return response()->json([
            'me' => $user
        ], Response::HTTP_OK);
    }
}
