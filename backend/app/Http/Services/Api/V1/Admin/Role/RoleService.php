<?php

namespace App\Http\Services\Api\V1\Admin\Role;

use Illuminate\Http\Request;

interface RoleService
{
    public function index(Request $request);

    public function getAllRoles();

    public function store(Request $request);

    public function show(int $id);

    public function update(int $id, Request $request);

    public function destroy(int $id);
}