<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Http\Response;

class PermissionController extends Controller
{
    public function index(User $user)
    {
        return response()->json([
            'user' => $user,
            'permission_tree_array' => $user->getPermissionTreeArray()
        ], Response::HTTP_OK);
    }

    public function update(User $user) {
        $allowed = request('allowed');
        $permission_code = request('permissionCode');

        $allowed ? $user->allowTo($permission_code) : $user->dontAllowTo($permission_code);

        return response()->json([
            'permission_tree_array' => $user->getPermissionTreeArray()
        ], Response::HTTP_OK);
    }

    public function getMyPermission()
    {
        $user = request()->user();
        try {
            return response()->json([
                'permissions' => $user->getPermissionCodes($user->getPermissionTreeArray())
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function getAllMyPermission()
    {
        try {
            return response()->json([
                'permissions' => request()->user()->getPermissionTreeArray()
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function checkPermission()
    {
        return response()->json([
            'hasPermission' => request()->user()->hasPermission(Permission::where('code', request('code'))->first())
        ], Response::HTTP_OK);
    }

    public function save(Request $request)
    {
        $user = User::find($request->user_id);

        $data = $request->toArray();
        unset($data['_token']);
        unset($data['user_id']);

        foreach ($data as $permission_code => $allowed) {
            if ($allowed) {
                $user->allowTo($permission_code);
            } else {
                $user->dontAllowTo($permission_code);
            }
        }

        return redirect('/admin/permissions/' . $user->id)
            ->with('message', 'Permissions have been successfully saved!');
    }
}
