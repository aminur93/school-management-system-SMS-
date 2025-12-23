<?php

namespace App\Http\Services\Api\V1\Admin\User;

use Illuminate\Http\Request;

interface UserService
{
    public function index(Request $request);

    public function getAllUsers();

    public function store(Request $request);

    public function show(int $id);

    public function update(Request $request, int $id);

    public function destroy(int $id);
}