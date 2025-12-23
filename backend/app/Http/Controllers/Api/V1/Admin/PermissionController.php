<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Helper\GlobalResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Admin\PermissionRequest;
use App\Http\Services\Api\V1\Admin\Permission\PermissionService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class PermissionController extends Controller
{
    protected PermissionService $permissionService;

    public function __construct(PermissionService $permissionService)
    {
        $this->permissionService = $permissionService;
    }

    public function index(Request $request)
    {
        //Convert pagination query to boolean
        $pagination = filter_var($request->get('pagination', true), FILTER_VALIDATE_BOOLEAN);

        // Fetch permissions via service
        $permissions = $pagination
            ? $this->permissionService->index($request)
            : $this->permissionService->getAllPermissions();


        // Return unified response
        $message = $pagination
            ? "All permissions fetched successfully with pagination"
            : "All permissions fetched successfully";

        return GlobalResponse::success($permissions, $message, Response::HTTP_OK);
    }

    public function store(PermissionRequest $request)
    {
        try {
           $permission = $this->permissionService->store($request);

           return GlobalResponse::success($permission, "Permission Store successful", Response::HTTP_CREATED);

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

            $permission = $this->permissionService->show($id);

            return GlobalResponse::success($permission, "Permission fetch successful", \Illuminate\Http\Response::HTTP_OK);

        }catch (ModelNotFoundException $exception){

            return GlobalResponse::error("Permission not found.", $exception->getMessage(), Response::HTTP_NOT_FOUND);

        }catch (\Exception $exception){

            return GlobalResponse::error("", $exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update($id, PermissionRequest $request)
    {
        try {

            $permission = $this->permissionService->update($id, $request);

            return GlobalResponse::success($permission, "Permission update successful", \Illuminate\Http\Response::HTTP_OK);

        }catch (ValidationException $exception){

            return GlobalResponse::error($exception->errors(), $exception->getResponse(), $exception->status);

        }catch (ModelNotFoundException $exception){

            return GlobalResponse::error("Permission not found.", $exception->getMessage(), Response::HTTP_NOT_FOUND);

        }catch (\Exception $exception){

            return GlobalResponse::error("", $exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy($id)
    {
        try {

            $this->permissionService->destroy($id);

            return GlobalResponse::success("", "Permission delete successful", \Illuminate\Http\Response::HTTP_OK);

        }catch (ModelNotFoundException $exception){

            return GlobalResponse::error("Permission not found.", $exception->getMessage(), Response::HTTP_NOT_FOUND);

        }catch (\Exception $exception){

            return GlobalResponse::error("", $exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}