<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Helper\GlobalResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Admin\StudentEnrollmentRequest;
use App\Http\Services\Api\V1\Admin\StudentEnrollment\StudentEnrollmentService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class StudentEnrollmentController extends Controller
{
    protected StudentEnrollmentService $studentEnrollmentService;

    public function __construct(StudentEnrollmentService $studentEnrollmentService)
    {
        $this->studentEnrollmentService = $studentEnrollmentService;
    }

    public function index(Request $request)
    {
        // Convert pagination query to boolean
        $pagination = filter_var($request->get('pagination', true), FILTER_VALIDATE_BOOLEAN);

        // Fetch student_enrollments via service
        $student_enrollments = $pagination
            ? $this->studentEnrollmentService->index($request)
            : $this->studentEnrollmentService->getAllStudentEnrollments();


        // Return unified response
        $message = $pagination
            ? "All student enrollments fetched successfully with pagination"
            : "All student enrollments fetched successfully";

        return GlobalResponse::success($student_enrollments, $message, Response::HTTP_OK);
    }

    public function store(StudentEnrollmentRequest $request)
    {
        try {
           $student_enrollments = $this->studentEnrollmentService->store($request);

           return GlobalResponse::success($student_enrollments, "Student enrollment Store successful", Response::HTTP_CREATED);

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

            $student_enrollments = $this->studentEnrollmentService->show($id);

            return GlobalResponse::success($student_enrollments, "Student enrollment fetch successful", Response::HTTP_OK);

        }catch (ModelNotFoundException $exception){

            return GlobalResponse::error("Student enrollment not found.", $exception->getMessage(), Response::HTTP_NOT_FOUND);

        }catch (Exception $exception){

            return GlobalResponse::error("", $exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

     public function update(StudentEnrollmentRequest $request, int $id)
    {
        try {

            $student_enrollments = $this->studentEnrollmentService->update($request, $id);

            return GlobalResponse::success($student_enrollments, "Student enrollment update successful", \Illuminate\Http\Response::HTTP_OK);

        }catch (ValidationException $exception){

            return GlobalResponse::error($exception->errors(), $exception->getResponse(), $exception->status);

        }catch (ModelNotFoundException $exception){

            return GlobalResponse::error("Student enrollment not found.", $exception->getMessage(), Response::HTTP_NOT_FOUND);

        }catch (Exception $exception){

            return GlobalResponse::error("", $exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy(int $id)
    {
        try {

            $this->studentEnrollmentService->destroy($id);

            return GlobalResponse::success("", "Student enrollment deleted successfully", Response::HTTP_NO_CONTENT);

        } catch (ModelNotFoundException $exception) {

            return GlobalResponse::error("Student enrollment not found.", $exception->getMessage(), Response::HTTP_NOT_FOUND);

        } catch (Exception $exception) {

            return GlobalResponse::error("", $exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function changeStatus(Request $request, int $id)
    {
        try {

            $student_enrollments = $this->studentEnrollmentService->changeStatus($request, $id);

            return GlobalResponse::success($student_enrollments, "Student enrollment status updated successfully", Response::HTTP_OK);

        }catch (ModelNotFoundException $exception){

            return GlobalResponse::error("Student enrollment not found.", $exception->getMessage(), Response::HTTP_NOT_FOUND);

        }catch (Exception $exception){

            return GlobalResponse::error("", $exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}