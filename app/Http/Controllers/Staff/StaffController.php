<?php

namespace App\Http\Controllers\Staff;

use App\Http\Requests\User\BrowseRequest;
use App\Http\Requests\User\CreateRequest;
use App\Http\Requests\User\UpdateRequest;
use App\Http\Requests\User\DeleteRequest;
use App\Http\Requests\User\ReadRequest;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class StaffController extends Controller
{
    public function index(BrowseRequest $request): JsonResponse
    {
        $columns = ['id', 'uuid', 'name', 'email', 'created_at', 'updated_at'];
        $users = User::select($columns)
            ->where('superadmin', '=', 0)
            ->paginate($request->integer('per_page', 10));

        return new JsonResponse($users, JsonResponse::HTTP_OK);
    }
    public function store(CreateRequest $request): JsonResponse
    {
        if (User::where('email', $email = $request->string('email'))->exists()) {
            throw ValidationException::withMessages([
                'email' => trans('validation.unique', [
                    'attribute' => trans('email')
                ]),
            ]);
        }
        $user = new User();
        $user->name = $request->name ?? Str::before($email, '@');
        $user->email = $email;
        $user->password = $request->string('password');
        $user->save();

        $user->roles()->sync($request->roles);

        return new JsonResponse($user->only(['uuid']), JsonResponse::HTTP_OK);
    }

    public function update(UpdateRequest $request, User $user): JsonResponse
    {
        if(!$request->user()?->hasPermission('role:browse')) {
            return response()->json(null, JsonResponse::HTTP_FORBIDDEN);
        }
        if ($user->isSuperAdmin()) {
            return response()->json(null, JsonResponse::HTTP_BAD_REQUEST);
        }

        $wheres = [
            ['email', '=', $email = $request->string('email')],
            ['id', '<>', $user->id],
        ];
        if (User::where($wheres)->exists()) {
            throw ValidationException::withMessages([
                'email' => trans('validation.unique', [
                    'attribute' => trans('email')
                ]),
            ]);
        }
        $user->name = $request->name ?? Str::before($email, '@');
        $user->email = $email;

        $newPassword = $request->string('password');
        if ($newPassword) {
            $user->password = $newPassword;
        }

        $user->save();

        $user->roles()->sync($request->roles);

        return new JsonResponse($user->only(['uuid']), JsonResponse::HTTP_OK);
    }

    public function show(ReadRequest $request, User $user): JsonResponse
    {
        if ($user->isSuperAdmin()) {
            return response()->json(null, JsonResponse::HTTP_BAD_REQUEST);
        }

        $user->loadMissing('roles');

        $data = $user->toArray();
        $data['roles'] = $user->roles()->pluck('id')->toArray();

        return response()->json($data);
    }

    public function destroy(DeleteRequest $request, User $user): JsonResponse
    {
        if ($user->isSuperAdmin()) {
            return response()->json(null, JsonResponse::HTTP_BAD_REQUEST);
        }

        $user->delete();

        return response()->json($user->only(['uuid']), JsonResponse::HTTP_NO_CONTENT);
    }
}
