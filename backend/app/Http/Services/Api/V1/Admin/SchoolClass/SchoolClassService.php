<?php

namespace App\Http\Services\Api\V1\Admin\SchoolClass;

use Illuminate\Http\Request;

interface SchoolClassService
{
    public function index(Request $request);

    public function getAllClasses();

    public function store(Request $request);

    public function show(int $id);

    public function update(Request $request, int $id);

    public function destroy(int $id);

    public function statusUpdate(int $id);
}