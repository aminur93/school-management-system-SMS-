<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Helper\GlobalResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Admin\StudentDropoutRequest;
use App\Http\Services\Api\V1\Admin\StudentDropout\StudentDropoutService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class StudentDropoutController extends Controller
{
    protected StudentDropoutService $studentDropoutService;

    public function __construct(StudentDropoutService $studentDropoutService)
    {
        $this->studentDropoutService = $studentDropoutService;
    }

    public function index(Request $request)
    {
        // Convert pagination query to boolean
        $pagination = filter_var($request->get('pagination', true), FILTER_VALIDATE_BOOLEAN);

        // Fetch student dropout year via service
        $studentDropouts = $pagination
            ? $this->studentDropoutService->index($request)
            : $this->studentDropoutService->getAllStudentDropouts();


        // Return unified response
        $message = $pagination
            ? "All student dropout fetched successfully with pagination"
            : "All student dropout fetched successfully";

        return GlobalResponse::success($studentDropouts, $message, Response::HTTP_OK);
    }

    public function store(StudentDropoutRequest $request)
    {
        try {
           $studentDropout = $this->studentDropoutService->store($request);

           return GlobalResponse::success($studentDropout, "Student dropout Store successful", Response::HTTP_CREATED);

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

            $studentDropout = $this->studentDropoutService->show($id);

            return GlobalResponse::success($studentDropout, "Student dropout fetch successful", Response::HTTP_OK);

        }catch (ModelNotFoundException $exception){

            return GlobalResponse::error("Student dropout not found.", $exception->getMessage(), Response::HTTP_NOT_FOUND);

        }catch (Exception $exception){

            return GlobalResponse::error("", $exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(StudentDropoutRequest $request, int $id)
    {
        try {

            $studentDropout = $this->studentDropoutService->update($request, $id);

            return GlobalResponse::success($studentDropout, "Student dropout update successful", Response::HTTP_OK);

        }catch (ValidationException $exception){

            return GlobalResponse::error($exception->errors(), $exception->getResponse(), $exception->status);

        }catch (ModelNotFoundException $exception){

            return GlobalResponse::error("Student dropout not found.", $exception->getMessage(), Response::HTTP_NOT_FOUND);

        }catch (\Exception $exception){

            return GlobalResponse::error("", $exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy(int $id)
    {
        try {

            $this->studentDropoutService->destroy($id);

            return GlobalResponse::success("", "Student dropout deleted successfully", Response::HTTP_NO_CONTENT);

        } catch (ModelNotFoundException $exception) {

            return GlobalResponse::error("Student dropout not found.", $exception->getMessage(), Response::HTTP_NOT_FOUND);

        } catch (Exception $exception) {

            return GlobalResponse::error("", $exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function changeStatus(Request $request, int $id)
    {
        try {

            $studentDropout = $this->studentDropoutService->changeStatus($request, $id);

            return GlobalResponse::success($studentDropout, "Student dropout status updated successfully", Response::HTTP_OK);

        }catch (ModelNotFoundException $exception){

            return GlobalResponse::error("Student dropout not found.", $exception->getMessage(), Response::HTTP_NOT_FOUND);

        }catch (Exception $exception){

            return GlobalResponse::error("", $exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}