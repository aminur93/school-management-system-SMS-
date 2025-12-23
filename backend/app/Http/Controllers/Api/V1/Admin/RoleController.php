<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Helper\GlobalResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Admin\RoleRequest;
use App\Http\Services\Api\V1\Admin\Role\RoleService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class RoleController extends Controller
{
    protected RoleService $roleService;

    public function __construct(RoleService $roleService)
    {
        $this->roleService = $roleService;
    }

    public function index(Request $request)
    {
        //Convert pagination query to boolean
        $pagination = filter_var($request->get('pagination', true), FILTER_VALIDATE_BOOLEAN);

        // Fetch permissions via service
        $roles = $pagination
            ? $this->roleService->index($request)
            : $this->roleService->getAllRoles();


        // Return unified response
        $message = $pagination
            ? "All roles fetched successfully with pagination"
            : "All roles fetched successfully";

        return GlobalResponse::success($roles, $message, Response::HTTP_OK);
    }

    public function store(RoleRequest $request)
    {
        try {
           $role = $this->roleService->store($request);

           return GlobalResponse::success($role, "Role Store successful", Response::HTTP_CREATED);

        } catch (ValidationException $exception) {

            return GlobalResponse::error($exception->errors(), $exception->getMessage(), $exception->getCode());

        } catch (HttpException $exception) {

            return GlobalResponse::error("", $exception->getMessage(), $exception->getCode());

        } catch (Exception $exception) {

            return GlobalResponse::error("", $exception->getMessage(), $exception->getCode());
        }
    }

    public function show($id)
    {
        try {

            $role = $this->roleService->show($id);

            return GlobalResponse::success($role, "Role fetch successful", \Illuminate\Http\Response::HTTP_OK);

        }catch (ModelNotFoundException $exception){

            return GlobalResponse::error("Role not found.", $exception->getMessage(), Response::HTTP_NOT_FOUND);

        }catch (\Exception $exception){

            return GlobalResponse::error("", $exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update($id, RoleRequest $request)
    {
        try {

            $role = $this->roleService->update($id, $request);

            return GlobalResponse::success($role, "Role update successful", \Illuminate\Http\Response::HTTP_OK);

        }catch (ValidationException $exception){

            return GlobalResponse::error($exception->errors(), $exception->getResponse(), $exception->status);

        }catch (ModelNotFoundException $exception){

            return GlobalResponse::error("Role not found.", $exception->getMessage(), Response::HTTP_NOT_FOUND);

        }catch (\Exception $exception){

            return GlobalResponse::error("", $exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy($id)
    {
        try {

            $this->roleService->destroy($id);

            return GlobalResponse::success("", "Role delete successful", \Illuminate\Http\Response::HTTP_OK);

        }catch (ModelNotFoundException $exception){

            return GlobalResponse::error("Permission not found.", $exception->getMessage(), Response::HTTP_NOT_FOUND);

        }catch (\Exception $exception){

            return GlobalResponse::error("", $exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}