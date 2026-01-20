<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Helper\GlobalResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Admin\StudentRequest;
use App\Http\Services\Api\V1\Admin\Student\StudentService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class StudentController extends Controller
{
    protected StudentService $studentService;

    public function __construct(StudentService $studentService)
    {
        $this->studentService = $studentService;
    }

    public function index(Request $request)
    {
        // Convert pagination query to boolean
        $pagination = filter_var($request->get('pagination', true), FILTER_VALIDATE_BOOLEAN);

        // Fetch student via service
        $students = $pagination
            ? $this->studentService->index($request)
            : $this->studentService->getAllStudents();


        // Return unified response
        $message = $pagination
            ? "All student fetched successfully with pagination"
            : "All student fetched successfully";

        return GlobalResponse::success($students, $message, Response::HTTP_OK);
    }

    public function store(StudentRequest $request)
    {
        try {
           $student = $this->studentService->store($request);

           return GlobalResponse::success($student, "Student Store successful", Response::HTTP_CREATED);

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

            $student = $this->studentService->show($id);

            return GlobalResponse::success($student, "Student fetch successful", Response::HTTP_OK);

        }catch (ModelNotFoundException $exception){

            return GlobalResponse::error("Student not found.", $exception->getMessage(), Response::HTTP_NOT_FOUND);

        }catch (\Exception $exception){

            return GlobalResponse::error("", $exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(StudentRequest $request, int $id)
    {
        try {

            $student = $this->studentService->update($request, $id);

            return GlobalResponse::success($student, "Student update successful", Response::HTTP_OK);

        }catch (ValidationException $exception){

            return GlobalResponse::error($exception->errors(), $exception->getResponse(), $exception->status);

        }catch (ModelNotFoundException $exception){

            return GlobalResponse::error("Student not found.", $exception->getMessage(), Response::HTTP_NOT_FOUND);

        }catch (\Exception $exception){

            return GlobalResponse::error("", $exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy(int $id)
    {
        try {

            $this->studentService->destroy($id);

            return GlobalResponse::success("", "Student deleted successfully", Response::HTTP_NO_CONTENT);

        } catch (ModelNotFoundException $exception) {

            return GlobalResponse::error("Student not found.", $exception->getMessage(), Response::HTTP_NOT_FOUND);

        } catch (Exception $exception) {

            return GlobalResponse::error("", $exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function changeStatus(Request $request, int $id)
    {
        try {

            $student = $this->studentService->changeStatus($request, $id);

            return GlobalResponse::success($student, "Student status updated successfully", Response::HTTP_OK);

        }catch (ModelNotFoundException $exception){

            return GlobalResponse::error("Student not found.", $exception->getMessage(), Response::HTTP_NOT_FOUND);

        }catch (Exception $exception){

            return GlobalResponse::error("", $exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}