<?php

namespace App\Http\Services\Api\V1\Admin\StudentDocument;

use Illuminate\Http\Request;

interface StudentDocumentService
{
    public function index(Request $request);

    public function getAllStudentDocuments();

    public function store(Request $request);

    public function show(int $id);

    public function update(Request $request, int $id);

    public function destroy(int $id);

    public function verifiy(Request $request, int $id);
}