<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Helper\GlobalResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Admin\SectionRequest;
use App\Http\Services\Api\V1\Admin\Section\SectionService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class SectionController extends Controller
{
    protected SectionService $sectionService;

    public function __construct(SectionService $sectionService)
    {
        $this->sectionService = $sectionService;
    }

    public function index(Request $request)
    {
        // Convert pagination query to boolean
        $pagination = filter_var($request->get('pagination', true), FILTER_VALIDATE_BOOLEAN);

        // Fetch sections via service
        $sections = $pagination
            ? $this->sectionService->index($request)
            : $this->sectionService->getAllSections();


        // Return unified response
        $message = $pagination
            ? "All school classes fetched successfully with pagination"
            : "All school classes fetched successfully";

        return GlobalResponse::success($sections, $message, Response::HTTP_OK);
    }

    public function store(SectionRequest $request)
    {
        try {
           $section = $this->sectionService->store($request);

           return GlobalResponse::success($section, "Section Store successful", Response::HTTP_CREATED);

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

            $section = $this->sectionService->show($id);

            return GlobalResponse::success($section, "Section fetch successful", \Illuminate\Http\Response::HTTP_OK);

        }catch (ModelNotFoundException $exception){

            return GlobalResponse::error("Section not found.", $exception->getMessage(), Response::HTTP_NOT_FOUND);

        }catch (\Exception $exception){

            return GlobalResponse::error("", $exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(SectionRequest $request, int $id)
    {
        try {
           $section = $this->sectionService->update($request, $id);

           return GlobalResponse::success($section, "Section Update successful", Response::HTTP_OK);

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
           $this->sectionService->destroy($id);

           return GlobalResponse::success(null, "Section Delete successful", Response::HTTP_NO_CONTENT);

        } catch (HttpException $exception) {

            return GlobalResponse::error("", $exception->getMessage(), $exception->getStatusCode());

        } catch (Exception $exception) {

            return GlobalResponse::error("", $exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function statusUpdate(int $id)
    {
        try {
           $section = $this->sectionService->statusUpdate($id);

           return GlobalResponse::success($section, "Section status update successful", Response::HTTP_OK);

        } catch (HttpException $exception) {

            return GlobalResponse::error("", $exception->getMessage(), $exception->getStatusCode());

        } catch (Exception $exception) {

            return GlobalResponse::error("", $exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}