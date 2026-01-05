<?php

namespace App\Http\Services\Api\V1\Admin\Student;

use Illuminate\Http\Request;

interface StudentService
{
    public function index(Request $request);

    public function getAllStudents();

    public function store(Request $request);

    public function show(int $id);

    public function update(Request $request, int $id);

    public function destroy(int $id);

    public function changeStatus(Request $request, int $id);
}