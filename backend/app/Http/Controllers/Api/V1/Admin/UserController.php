<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Helper\GlobalResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Admin\UserRequest;
use App\Http\Services\Api\V1\Admin\User\UserService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class UserController extends Controller
{
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index(Request $request)
    {
         //Convert pagination query to boolean
        $pagination = filter_var($request->get('pagination', true), FILTER_VALIDATE_BOOLEAN);

        // Fetch permissions via service
        $users = $pagination
            ? $this->userService->index($request)
            : $this->userService->getAllUsers();


        // Return unified response
        $message = $pagination
            ? "All users fetched successfully with pagination"
            : "All users fetched successfully";

        return GlobalResponse::success($users, $message, Response::HTTP_OK);
    }

    public function store(UserRequest $request)
    {
        try {
           $user = $this->userService->store($request);

           return GlobalResponse::success($user, "User Store successful", Response::HTTP_CREATED);

        } catch (ValidationException $exception) {

            return GlobalResponse::error($exception->errors(), $exception->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);

        } catch (HttpException $exception) {

            return GlobalResponse::error("", $exception->getMessage(), $exception->getStatusCode());

        } catch (Exception $exception) {

            return GlobalResponse::error("", $exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(int $id)
    {
        try {

            $user = $this->userService->show($id);

            return GlobalResponse::success($user, "User fetch successful", \Illuminate\Http\Response::HTTP_OK);

        }catch (ModelNotFoundException $exception){

            return GlobalResponse::error("User not found.", $exception->getMessage(), Response::HTTP_NOT_FOUND);

        }catch (\Exception $exception){

            return GlobalResponse::error("", $exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(UserRequest $request, int $id)
    {
        try {

            $user = $this->userService->update($request, $id);

            return GlobalResponse::success($user, "User update successful", Response::HTTP_OK);

        } catch (ModelNotFoundException $exception) {

            return GlobalResponse::error("User not found.", $exception->getMessage(), Response::HTTP_NOT_FOUND);

        } catch (ValidationException $exception) {

            return GlobalResponse::error($exception->errors(), $exception->getMessage(), $exception->getCode());

        } catch (HttpException $exception) {

            return GlobalResponse::error("", $exception->getMessage(), $exception->getCode());

        } catch (Exception $exception) {

            return GlobalResponse::error("", $exception->getMessage(), $exception->getCode());
        }
    }

    public function destroy(int $id)
    {
        try {

            $this->userService->destroy($id);

            return GlobalResponse::success(null, "User delete successful", Response::HTTP_NO_CONTENT);

        } catch (ModelNotFoundException $exception) {

            return GlobalResponse::error("User not found.", $exception->getMessage(), Response::HTTP_NOT_FOUND);

        } catch (HttpException $exception) {

            return GlobalResponse::error("", $exception->getMessage(), $exception->getCode());

        } catch (Exception $exception) {

            return GlobalResponse::error("", $exception->getMessage(), $exception->getCode());
        }
    }
}