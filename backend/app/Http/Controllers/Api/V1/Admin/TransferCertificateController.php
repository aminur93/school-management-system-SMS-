<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Helper\GlobalResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Admin\TransferCertificateRequest;
use App\Http\Services\Api\V1\Admin\TransferCertificate\TransferCertificateService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class TransferCertificateController extends Controller
{
    protected TransferCertificateService $transferCertificateService;

    public function __construct(TransferCertificateService $transferCertificateService)
    {
        $this->transferCertificateService = $transferCertificateService;
    }

    public function index(Request $request)
    {
         // Convert pagination query to boolean
        $pagination = filter_var($request->get('pagination', true), FILTER_VALIDATE_BOOLEAN);

        // Fetch transfer certificate year via service
        $transfer_certificates = $pagination
            ? $this->transferCertificateService->index($request)
            : $this->transferCertificateService->getAllTransferCertificates();


        // Return unified response
        $message = $pagination
            ? "All transfer certificate year fetched successfully with pagination"
            : "All transfer certificate year fetched successfully";

        return GlobalResponse::success($transfer_certificates, $message, Response::HTTP_OK);
    }

    public function store(TransferCertificateRequest $request)
    {
        try {
           $transfer_certificate = $this->transferCertificateService->store($request);

           return GlobalResponse::success($transfer_certificate, "Transfer certificate year Store successful", Response::HTTP_CREATED);

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

            $transfer_certificate = $this->transferCertificateService->show($id);

            return GlobalResponse::success($transfer_certificate, "Transfer certificate year fetch successful", Response::HTTP_OK);

        }catch (ModelNotFoundException $exception){

            return GlobalResponse::error("Transfer certificate year not found.", $exception->getMessage(), Response::HTTP_NOT_FOUND);

        }catch (Exception $exception){

            return GlobalResponse::error("", $exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(TransferCertificateRequest $request, int $id)
    {
        try {

            $transfer_certificate = $this->transferCertificateService->update($request, $id);

            return GlobalResponse::success($transfer_certificate, "Transfer certificate year update successful", Response::HTTP_OK);

        }catch (ValidationException $exception){

            return GlobalResponse::error($exception->errors(), $exception->getResponse(), $exception->status);

        }catch (ModelNotFoundException $exception){

            return GlobalResponse::error("Transfer certificate year not found.", $exception->getMessage(), Response::HTTP_NOT_FOUND);

        }catch (Exception $exception){

            return GlobalResponse::error("", $exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy(int $id)
    {
        try {

            $this->transferCertificateService->destroy($id);

            return GlobalResponse::success("", "Transfer certificate year deleted successfully", Response::HTTP_NO_CONTENT);

        } catch (ModelNotFoundException $exception) {

            return GlobalResponse::error("Transfer certificate year not found.", $exception->getMessage(), Response::HTTP_NOT_FOUND);

        } catch (Exception $exception) {

            return GlobalResponse::error("", $exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function changeStatus(Request $request, int $id)
    {
        try {

            $transfer_certificate = $this->transferCertificateService->changeStatus($request, $id);

            return GlobalResponse::success($transfer_certificate, "Transfer certificate year status updated successfully", Response::HTTP_OK);

        }catch (ModelNotFoundException $exception){

            return GlobalResponse::error("Transfer certificate year not found.", $exception->getMessage(), Response::HTTP_NOT_FOUND);

        }catch (Exception $exception){

            return GlobalResponse::error("", $exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}