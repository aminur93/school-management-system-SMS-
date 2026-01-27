<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Helper\GlobalResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Admin\StudentDocumentRequest;
use App\Http\Services\Api\V1\Admin\StudentDocument\StudentDocumentService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class StudentDocumentController extends Controller
{
    protected StudentDocumentService $studentDocumentService;

    public function __construct(StudentDocumentService $studentDocumentService)
    {
        $this->studentDocumentService = $studentDocumentService;
    }

    public function index(Request $request)
    {
        // Convert pagination query to boolean
        $pagination = filter_var($request->get('pagination', true), FILTER_VALIDATE_BOOLEAN);

        // Fetch student document via service
        $student_documents = $pagination
            ? $this->studentDocumentService->index($request)
            : $this->studentDocumentService->getAllStudentDocuments();


        // Return unified response
        $message = $pagination
            ? "All student document fetched successfully with pagination"
            : "All student document fetched successfully";

        return GlobalResponse::success($student_documents, $message, Response::HTTP_OK);
    }

    public function store(StudentDocumentRequest $request)
    {
        try {
           $student_document = $this->studentDocumentService->store($request);

           return GlobalResponse::success($student_document, "Student Document Store successful", Response::HTTP_CREATED);

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

            $student_document = $this->studentDocumentService->show($id);

            return GlobalResponse::success($student_document, "Student Document fetch successful", Response::HTTP_OK);

        }catch (ModelNotFoundException $exception){

            return GlobalResponse::error("Student Document not found.", $exception->getMessage(), Response::HTTP_NOT_FOUND);

        }catch (\Exception $exception){

            return GlobalResponse::error("", $exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(StudentDocumentRequest $request, int $id)
    {
        try {

            $student_document = $this->studentDocumentService->update($request, $id);
            
            return GlobalResponse::success($student_document, "Student Document update successful", Response::HTTP_OK);

        }catch (ValidationException $exception){

            return GlobalResponse::error($exception->errors(), $exception->getResponse(), $exception->status);

        }catch (ModelNotFoundException $exception){

            return GlobalResponse::error("Student Document not found.", $exception->getMessage(), Response::HTTP_NOT_FOUND);

        }catch (\Exception $exception){

            return GlobalResponse::error("", $exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy(int $id)
    {
        try {

            $this->studentDocumentService->destroy($id);

            return GlobalResponse::success("", "Student Document deleted successfully", Response::HTTP_NO_CONTENT);

        } catch (ModelNotFoundException $exception) {

            return GlobalResponse::error("Student Document not found.", $exception->getMessage(), Response::HTTP_NOT_FOUND);
            
        } catch (Exception $exception) {

            return GlobalResponse::error("", $exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function verifiy(Request $request, int $id)
    {
        try {

            $student_document = $this->studentDocumentService->verifiy($request, $id);

            return GlobalResponse::success($student_document, "Student Document verification successful", Response::HTTP_OK);

        } catch (ModelNotFoundException $exception) {

            return GlobalResponse::error("Student Document not found.", $exception->getMessage(), Response::HTTP_NOT_FOUND);

        } catch (Exception $exception) {

            return GlobalResponse::error("", $exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}