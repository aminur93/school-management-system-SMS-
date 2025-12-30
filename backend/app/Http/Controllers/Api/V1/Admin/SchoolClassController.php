<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Helper\GlobalResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Admin\SchoolClassRequest;
use App\Http\Services\Api\V1\Admin\SchoolClass\SchoolClassService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class SchoolClassController extends Controller
{
    protected SchoolClassService $schoolClassService;

    public function __construct(SchoolClassService $schoolClassService)
    {
        $this->schoolClassService = $schoolClassService;
    }

    public function index(Request $request)
    {
        // Convert pagination query to boolean
        $pagination = filter_var($request->get('pagination', true), FILTER_VALIDATE_BOOLEAN);

        // Fetch school classes via service
        $schoolClasses = $pagination
            ? $this->schoolClassService->index($request)
            : $this->schoolClassService->getAllClasses();


        // Return unified response
        $message = $pagination
            ? "All school classes fetched successfully with pagination"
            : "All school classes fetched successfully";

        return GlobalResponse::success($schoolClasses, $message, Response::HTTP_OK);
    }

    public function store(SchoolClassRequest $request)
    {
        try {
           $schoolClass = $this->schoolClassService->store($request);

           return GlobalResponse::success($schoolClass, "School Class Store successful", Response::HTTP_CREATED);

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

            $schoolClass = $this->schoolClassService->show($id);

            return GlobalResponse::success($schoolClass, "School Class fetch successful", \Illuminate\Http\Response::HTTP_OK);
            
        }catch (ModelNotFoundException $exception){

            return GlobalResponse::error("School Class not found.", $exception->getMessage(), Response::HTTP_NOT_FOUND);

        }catch (\Exception $exception){

            return GlobalResponse::error("", $exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(SchoolClassRequest $request, int $id)
    {
        try {

            $schoolClass = $this->schoolClassService->update($request, $id);

            return GlobalResponse::success($schoolClass, "School Class update successful", Response::HTTP_OK);

        } catch (ModelNotFoundException $exception) {

            return GlobalResponse::error("School Class not found.", $exception->getMessage(), Response::HTTP_NOT_FOUND);

        } catch (ValidationException $exception) {

            return GlobalResponse::error($exception->errors(), $exception->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);

        } catch (HttpException $exception) {

            return GlobalResponse::error("", $exception->getMessage(), $exception->getStatusCode());

        } catch (Exception $exception) {

            return GlobalResponse::error("", $exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy(int $id)
    {
        try {

            $this->schoolClassService->destroy($id);

            return GlobalResponse::success("", "School Class deleted successfully", Response::HTTP_NO_CONTENT);

        } catch (ModelNotFoundException $exception) {

            return GlobalResponse::error("School Class not found.", $exception->getMessage(), Response::HTTP_NOT_FOUND);

        } catch (HttpException $exception) {

            return GlobalResponse::error("", $exception->getMessage(), $exception->getStatusCode());

        } catch (Exception $exception) {

            return GlobalResponse::error("", $exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function statusUpdate(int $id)
    {
        try {

            $schoolClass = $this->schoolClassService->statusUpdate($id);

            return GlobalResponse::success($schoolClass, "School Class status updated successfully", Response::HTTP_OK);

        } catch (ModelNotFoundException $exception) {

            return GlobalResponse::error("School Class not found.", $exception->getMessage(), Response::HTTP_NOT_FOUND);

        } catch (HttpException $exception) {

            return GlobalResponse::error("", $exception->getMessage(), $exception->getStatusCode());

        } catch (Exception $exception) {

            return GlobalResponse::error("", $exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}