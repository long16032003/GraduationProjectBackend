<?php

namespace App\Http\Controllers;

use App\Http\Requests\Role\BrowseRequest;
use App\Http\Requests\Role\CreateRequest;
use App\Http\Requests\Role\DeleteRequest;
use App\Http\Requests\Role\ReadRequest;
use App\Http\Requests\Role\UpdateRequest;
use App\Models\Role;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class RoleController extends Controller
{
    public function index(BrowseRequest $request): JsonResponse
    {
        $columns = ['id', 'name', 'level', 'status', 'created_at', 'updated_at'];
        $roles = Role::select($columns)
            ->paginate($request->integer('per_page', 10));

        return new JsonResponse($roles, JsonResponse::HTTP_OK);
    }
    public function store(CreateRequest $request): JsonResponse
    {
        if (Role::where('name', $request->name)->exists()) {
            throw ValidationException::withMessages([
                'name' => trans('validation.unique', [
                    'attribute' => trans('name')
                ]),
            ]);
        }
        $role = new Role();
        $role->name = $request->name;
        $role->level = $request->integer('level');
        $role->status = $request->boolean('status');
        $role->permissions = $request
            ->collect('permissions')
            ->filter(static function ($value, $permission) {
                if (!in_array($permission, config('permission.flat', []), true)) {
                    return false;
                }
                return (bool) $value;
            })->all();

        $role->save();

        return new JsonResponse($request->all(), JsonResponse::HTTP_OK);
    }

    public function update(UpdateRequest $request, Role $role): JsonResponse
    {
        $wheres = [
            ['name', '=', $request->name],
            ['id', '<>', $role->id],
        ];
        if (Role::where($wheres)->exists()) {
            throw ValidationException::withMessages([
                'name' => trans('validation.unique', [
                    'attribute' => trans('name')
                ]),
            ]);
        }
        $role->name = $request->name;
        $role->level = $request->integer('level');
        $role->status = $request->boolean('status');
        $role->permissions = $request
            ->collect('permissions')
            ->filter(static function ($value, $permission) {
                if (!in_array($permission, config('permission.flat', []), true)) {
                    return false;
                }
                return (bool) $value;
            })->all();

        $role->save();

        return new JsonResponse($request->all(), JsonResponse::HTTP_OK);
    }

    public function show(ReadRequest $request, Role $role): JsonResponse
    {
        return response()->json($role);
    }

    public function destroy(DeleteRequest $request, Role $role): JsonResponse
    {
        $role->delete();

        return response()->json(null, JsonResponse::HTTP_NO_CONTENT);
    }
}
