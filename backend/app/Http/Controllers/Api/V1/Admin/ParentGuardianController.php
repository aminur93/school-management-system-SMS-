<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Helper\GlobalResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Admin\ParentGuardianRequest;
use App\Http\Services\Api\V1\Admin\ParentGuardian\ParentGuardianService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ParentGuardianController extends Controller
{
    protected ParentGuardianService $parentGuardianService;

    public function __construct(ParentGuardianService $parentGuardianService)
    {
        $this->parentGuardianService = $parentGuardianService;
    }

    public function index(Request $request)
    {
        // Convert pagination query to boolean
        $pagination = filter_var($request->get('pagination', true), FILTER_VALIDATE_BOOLEAN);

        // Fetch parent guardian year via service
        $parent_guardians = $pagination
            ? $this->parentGuardianService->index($request)
            : $this->parentGuardianService->getAllParentGuardians();


        // Return unified response
        $message = $pagination
            ? "All parent guardian fetched successfully with pagination"
            : "All parent guardian fetched successfully";

        return GlobalResponse::success($parent_guardians, $message, Response::HTTP_OK);
    }

    public function store(ParentGuardianRequest $request)
    {
        try {
           $parent_guardian = $this->parentGuardianService->store($request);

           return GlobalResponse::success($parent_guardian, "Parent guardian Store successful", Response::HTTP_CREATED);

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

            $parent_guardian = $this->parentGuardianService->show($id);

            return GlobalResponse::success($parent_guardian, "Parent guardian fetch successful", \Illuminate\Http\Response::HTTP_OK);

        }catch (ModelNotFoundException $exception){

            return GlobalResponse::error("Parent guardian year not found.", $exception->getMessage(), Response::HTTP_NOT_FOUND);

        }catch (\Exception $exception){

            return GlobalResponse::error("", $exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(ParentGuardianRequest $request, int $id)
    {
        try {

            $parent_guardian = $this->parentGuardianService->update($request, $id);

            return GlobalResponse::success($parent_guardian, "Parent guardian update successful", \Illuminate\Http\Response::HTTP_OK);

        }catch (ValidationException $exception){

            return GlobalResponse::error($exception->errors(), $exception->getResponse(), $exception->status);

        }catch (ModelNotFoundException $exception){

            return GlobalResponse::error("Parent guardian not found.", $exception->getMessage(), Response::HTTP_NOT_FOUND);

        }catch (\Exception $exception){

            return GlobalResponse::error("", $exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy(int $id)
    {
        try {

            $this->parentGuardianService->destroy($id);

            return GlobalResponse::success("", "Parent guardian deleted successfully", Response::HTTP_NO_CONTENT);

        } catch (ModelNotFoundException $exception) {

            return GlobalResponse::error("Parent guardian not found.", $exception->getMessage(), Response::HTTP_NOT_FOUND);

        } catch (Exception $exception) {

            return GlobalResponse::error("", $exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}