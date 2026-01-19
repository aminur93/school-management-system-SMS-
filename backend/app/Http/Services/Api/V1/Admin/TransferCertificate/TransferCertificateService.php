<?php

namespace App\Http\Services\Api\V1\Admin\TransferCertificate;

use Illuminate\Http\Request;

interface TransferCertificateService
{
    public function index(Request $request);

    public function getAllTransferCertificates();

    public function store(Request $request);

    public function show(int $id);

    public function update(Request $request, int $id);

    public function destroy(int $id);

    public function changeStatus(Request $request, int $id);
}