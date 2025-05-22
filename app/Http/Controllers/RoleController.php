<?php

namespace App\Http\Controllers;

use App\Http\Requests\RoleStoreRequest;
use Illuminate\Http\JsonResponse;

class RoleController extends Controller
{
    public function store(RoleStoreRequest $request): JsonResponse
    {
        return new JsonResponse(config('role'), JsonResponse::HTTP_OK);
    }
}
