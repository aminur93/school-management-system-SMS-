<?php

namespace App\Http\Services\Api\V1\Admin\ParentGuardian;

use Illuminate\Http\Request;

interface ParentGuardianService
{
    public function index(Request $request);

    public function getAllParentGuardians();

    public function store(Request $request);

    public function show(int $id);

    public function update(Request $request, int $id);

    public function destroy(int $id);
}