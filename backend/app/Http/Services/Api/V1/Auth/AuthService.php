<?php

namespace App\Http\Services\Api\V1\Auth;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

interface AuthService
{
    public function login(Request $request);

    public function refreshToken(string $refreshToken);

    public function logout();

    public function register(Request $request);
}