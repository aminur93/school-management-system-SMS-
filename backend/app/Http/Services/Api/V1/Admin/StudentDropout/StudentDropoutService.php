<?php

namespace App\Http\Services\Api\V1\Admin\StudentDropout;

use Illuminate\Http\Request;

interface StudentDropoutService
{
    public function index(Request $request);

    public function getAllStudentDropouts();

    public function store(Request $request);

    public function show(int $id);

    public function update(Request $request, int $id);

    public function destroy(int $id);

    public function changeStatus(Request $request, int $id);
}