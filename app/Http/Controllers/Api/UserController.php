<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Symfony\Component\HttpFoundation\JsonResponse;

class UserController extends Controller
{

    public function index(): JsonResponse
    {
        return new JsonResponse(User::all());
    }
}
