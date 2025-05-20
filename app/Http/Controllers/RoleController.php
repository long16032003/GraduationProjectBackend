<?php

namespace App\Http\Controllers;

use App\Http\Requests\RoleStoreRequest;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function store(RoleStoreRequest $request): JsonResponse
    {
        return new JsonResponse(config('role'), Response::HTTP_OK);
    }
}
