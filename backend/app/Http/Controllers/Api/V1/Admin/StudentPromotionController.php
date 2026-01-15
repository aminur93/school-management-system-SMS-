<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Helper\GlobalResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Admin\StudentPromotionRequest;
use App\Http\Services\Api\V1\Admin\StudentPromotion\StudentPromotionService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class StudentPromotionController extends Controller
{
    protected StudentPromotionService $studentPromotionService;

    public function __construct(StudentPromotionService $studentPromotionService)
    {
        $this->studentPromotionService = $studentPromotionService;
    }

    public function index(Request $request)
    {
        // Convert pagination query to boolean
        $pagination = filter_var($request->get('pagination', true), FILTER_VALIDATE_BOOLEAN);

        // Fetch student promotion year via service
        $student_promotions = $pagination
            ? $this->studentPromotionService->index($request)
            : $this->studentPromotionService->getAllStudentPromotions();


        // Return unified response
        $message = $pagination
            ? "All student promotion year fetched successfully with pagination"
            : "All student promotion year fetched successfully";

        return GlobalResponse::success($student_promotions, $message, Response::HTTP_OK);
    }

    public function store(StudentPromotionRequest $request)
    {
        try {
           $student_promotion = $this->studentPromotionService->store($request);

           return GlobalResponse::success($student_promotion, "Student promotion Store successful", Response::HTTP_CREATED);

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

            $student_promotion = $this->studentPromotionService->show($id);

            return GlobalResponse::success($student_promotion, "Student promotion fetch successful", Response::HTTP_OK);

        }catch (ModelNotFoundException $exception){

            return GlobalResponse::error("Student promotion year not found.", $exception->getMessage(), Response::HTTP_NOT_FOUND);

        }catch (\Exception $exception){

            return GlobalResponse::error("", $exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(StudentPromotionRequest $request, int $id)
    {
        try {

            $student_promotion = $this->studentPromotionService->update($request, $id);

            return GlobalResponse::success($student_promotion, "Student promotion update successful", Response::HTTP_OK);

        }catch (ValidationException $exception){

            return GlobalResponse::error($exception->errors(), $exception->getResponse(), $exception->status);

        }catch (ModelNotFoundException $exception){

            return GlobalResponse::error("Student promotion not found.", $exception->getMessage(), Response::HTTP_NOT_FOUND);

        }catch (\Exception $exception){

            return GlobalResponse::error("", $exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy(int $id)
    {
        try {

            $this->studentPromotionService->destroy($id);

            return GlobalResponse::success("", "Student promotion deleted successfully", Response::HTTP_NO_CONTENT);

        } catch (ModelNotFoundException $exception) {

            return GlobalResponse::error("Student promotion not found.", $exception->getMessage(), Response::HTTP_NOT_FOUND);

        } catch (Exception $exception) {

            return GlobalResponse::error("", $exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function changeStatus(Request $request, int $id)
    {
        try {

            $student_promotion = $this->studentPromotionService->changeStatus($request, $id);

            return GlobalResponse::success($student_promotion, "Student promotion result status updated successfully", Response::HTTP_OK);

        }catch (ModelNotFoundException $exception){

            return GlobalResponse::error("Student promotion not found.", $exception->getMessage(), Response::HTTP_NOT_FOUND);

        }catch (Exception $exception){

            return GlobalResponse::error("", $exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}