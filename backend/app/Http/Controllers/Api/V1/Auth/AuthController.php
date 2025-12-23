<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Helper\GlobalResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Auth\LoginRequest;
use App\Http\Requests\Api\V1\Auth\RegisterRequest;
use App\Http\Services\Api\V1\Auth\AuthService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class AuthController extends Controller
{
    private $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function login(LoginRequest $request)
    {
        try {
            
            $login = $this->authService->login($request);

            return GlobalResponse::success($login, "Login successful", Response::HTTP_OK);

        } catch (ValidationException $exception) {

            return GlobalResponse::error($exception->errors(), $exception->getMessage(), $exception->getCode());

        } catch (HttpException $exception) {

            return GlobalResponse::error("", $exception->getMessage(), $exception->getStatusCode());
            
        } catch (\Throwable $th) {
            
            return GlobalResponse::error("", $th->getMessage(), $th->getCode());

        } 
    }

    public function refreshToken(Request $request)
    {
        try {
            
            $refreshToken = $this->authService->refreshToken($request->refreshToken);

            return GlobalResponse::success($refreshToken, "Generate new token successfully", Response::HTTP_OK);

        } catch (HttpException $exception) {

            return GlobalResponse::error("", $exception->getMessage(), $exception->getStatusCode());

        } catch (\Throwable $th) {

            return GlobalResponse::error("", $th->getMessage(), $th->getCode());
        }
    }

    public function logout()
    {
        $this->authService->logout();

        return GlobalResponse::success("", "Logout successful", Response::HTTP_OK);
    }

    public function register(RegisterRequest $request)
    {
        try {
            
            $register = $this->authService->register($request);

            return GlobalResponse::success($register, "Register successful", Response::HTTP_CREATED);

        } catch (ValidationException $exception) {

            return GlobalResponse::error($exception->errors(), $exception->getMessage(), $exception->getCode());

        } catch (HttpException $exception) {

            return GlobalResponse::error("", $exception->getMessage(), $exception->getStatusCode());
            
        } catch (\Throwable $th) {
            
            return GlobalResponse::error("", $th->getMessage(), $th->getCode());

        } catch (Exception $exception) {

            return GlobalResponse::error("", $exception->getMessage(), $exception->getCode());
        }
    }
}