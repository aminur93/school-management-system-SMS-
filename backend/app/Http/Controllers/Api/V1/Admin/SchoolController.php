<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Helper\GlobalResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Admin\SchoolRequest;
use App\Http\Services\Api\V1\Admin\School\SchoolService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class SchoolController extends Controller
{
    protected SchoolService $schoolService;

    public function __construct(SchoolService $schoolService)
    {
        $this->schoolService = $schoolService;
    }

    public function index(Request $request)
    {
        // Convert pagination query to boolean
        $pagination = filter_var($request->get('pagination', true), FILTER_VALIDATE_BOOLEAN);

        // Fetch school year via service
        $schools = $pagination
            ? $this->schoolService->index($request)
            : $this->schoolService->getAllSchools();


        // Return unified response
        $message = $pagination
            ? "All school year fetched successfully with pagination"
            : "All school year fetched successfully";

        return GlobalResponse::success($schools, $message, Response::HTTP_OK);
    }

    public function store(SchoolRequest $request)
    {
        try {
           $school = $this->schoolService->store($request);

           return GlobalResponse::success($school, "School year Store successful", Response::HTTP_CREATED);

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

            $school = $this->schoolService->show($id);

            return GlobalResponse::success($school, "School year fetch successful", Response::HTTP_OK);

        }catch (ModelNotFoundException $exception){

            return GlobalResponse::error("School year not found.", $exception->getMessage(), Response::HTTP_NOT_FOUND);

        }catch (\Exception $exception){

            return GlobalResponse::error("", $exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(SchoolRequest $request, int $id)
    {
        try {

            $school = $this->schoolService->update($request, $id);

            return GlobalResponse::success($school, "School year update successful", \Illuminate\Http\Response::HTTP_OK);

        }catch (ValidationException $exception){

            return GlobalResponse::error($exception->errors(), $exception->getResponse(), $exception->status);

        }catch (ModelNotFoundException $exception){

            return GlobalResponse::error("School year not found.", $exception->getMessage(), Response::HTTP_NOT_FOUND);

        }catch (\Exception $exception){

            return GlobalResponse::error("", $exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy(int $id)
    {
        try {

            $this->schoolService->destroy($id);

            return GlobalResponse::success("", "School year deleted successfully", Response::HTTP_NO_CONTENT);

        } catch (ModelNotFoundException $exception) {

            return GlobalResponse::error("School year not found.", $exception->getMessage(), Response::HTTP_NOT_FOUND);

        } catch (Exception $exception) {

            return GlobalResponse::error("", $exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

     public function changeStatus(int $id, Request $request)
    {
        try {

            $school = $this->schoolService->changeStatus($id, $request->is_active);

            return GlobalResponse::success($school, "School year status updated successfully", Response::HTTP_OK);

        }catch (ModelNotFoundException $exception){

            return GlobalResponse::error("School year not found.", $exception->getMessage(), Response::HTTP_NOT_FOUND);

        }catch (\Exception $exception){

            return GlobalResponse::error("", $exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}