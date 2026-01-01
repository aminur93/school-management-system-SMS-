<?php

namespace App\Http\Services\Api\V1\Admin\Section;

use Illuminate\Http\Request;

interface SectionService
{
    public function index(Request $request);

    public function getAllSections();

    public function store(Request $request);

    public function show(int $id);

    public function update(Request $request, int $id);

    public function destroy(int $id);

    public function statusUpdate(int $id);
}