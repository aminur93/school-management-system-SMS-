<?php

namespace App\Http\Services\Api\V1\Admin\Permission;

use Illuminate\Http\Request;
use Ramsey\Uuid\Type\Integer;

interface PermissionService
{
    public function index(Request $request);

    public function getAllPermissions();

    public function store(Request $request);

    public function show(int $id);

    public function update(int $id, Request $request);

    public function destroy(int $id);
}