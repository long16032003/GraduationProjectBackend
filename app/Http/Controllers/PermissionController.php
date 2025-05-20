<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PermissionController extends Controller
{
    public function show(Request $request): JsonResponse
    {
       return new JsonResponse(config('permission'), Response::HTTP_OK);
    }
}
