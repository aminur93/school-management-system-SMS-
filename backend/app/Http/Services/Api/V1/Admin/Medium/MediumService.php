<?php

namespace App\Http\Services\Api\V1\Admin\Medium;

use Illuminate\Http\Request;

interface MediumService
{
    public function index(Request $request);

    public function getAllActiveMedia();

    public function store(Request $request);

    public function show(int $id);

    public function update(int $id, Request $request);
    
    public function destroy(int $id);

    public function statusUpdate(int $id, bool $isActive);
}