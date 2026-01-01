<?php

namespace App\Http\Services\Api\V1\Admin\Section;

use App\Http\Resources\Api\V1\Admin\SectionResource;
use App\Models\Section;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SectionServiceImpl implements SectionService
{
    public function index(Request $request)
    {
        $section = Section::with('schoolClass', 'user');

         // Sorting (secure)
        $sortableColumns = ['id', 'name', 'capacity', 'room_number', 'created_at'];

        $sortBy = $request->get('sortBy', 'id');
        $sortDesc = $request->get('sortDesc', 'true') === 'true' ? 'desc' : 'asc';

        if (! in_array($sortBy, $sortableColumns)) {
            $sortBy = 'id';
        }

        $section->orderBy($sortBy, $sortDesc);

        // Search
        if ($search = $request->get('search')) {
            $section->where('name', 'like', "%{$search}%")
                ->orWhere('capacity', 'like', "%{$search}%")
                ->orWhere('room_number', 'like', "%{$search}%");
        }

        // Pagination
        $itemsPerPage = (int) $request->get('itemsPerPage', 10);
        $sections = $section->paginate($itemsPerPage);

        return SectionResource::collection($sections);
    }

    public function getAllSections()
    {
        $sections = Section::with('schoolClass', 'user')->latest()->get();

        return SectionResource::collection($sections);
    }

    public function store(Request $request)
    {
        // Call MySQL FUNCTION
        $result = DB::selectOne(
            "SELECT fn_sections_store(?, ?, ?, ?, ?, ?) AS section_id",
            [
                $request->school_class_id,
                $request->name,
                $request->capacity,
                $request->room_number,
                $request->is_active,
                $request->created_by
            ]
        );

        $insertedId = $result->section_id ?? null;

        if (!$insertedId) {
            throw new \Exception("Failed to create section");
        }

        // Optionally return the inserted row
        return DB::table('sections')->where('id', $insertedId)->first();
    }

    public function show(int $id)
    {
        $section = Section::with('schoolClass', 'user')->findOrFail($id);

        return new SectionResource($section);
    }

    public function update(Request $request, int $id)
    {
        if (!isset($id)) {
            throw new Exception("Section ID is required");
        }

        // Call MySQL FUNCTION
        $result = DB::selectOne(
            "SELECT fn_sections_update(?, ?, ?, ?, ?, ?) AS updated",
            [
                $request->id,
                $request->school_class_id,
                $request->name,
                $request->capacity,
                $request->room_number,
                $request->is_active
            ]
        );

        $affectedRows = $result->updated ?? 0;

        if ($affectedRows === 0) {
            throw new Exception("No section updated or section not found");
        }

        // Optionally fetch the updated row
        return DB::table('sections')->where('id', $request->id)->first();
    }

    public function destroy(int $id)
    {
         // Call MySQL FUNCTION
        $result = DB::selectOne(
            "SELECT fn_sections_delete(?) AS deleted",
            [$id]
        );

        $deletedRows = $result->deleted ?? 0;

        if ($deletedRows === 0) {
            throw new Exception("Section not found or could not be deleted");
        }

        return true;
    }

    public function statusUpdate(int $id)
    {
        // Call MySQL FUNCTION
        $result = DB::selectOne(
            "SELECT fn_sections_toggle_status(?) AS new_status",
            [$id]
        );

        if (!isset($result->new_status)) {
            throw new Exception("Section not found or could not toggle status");
        }

        // new_status is returned as 0/1 → convert to bool
         return DB::table('sections')->where('id', $id)->first();
    }
}