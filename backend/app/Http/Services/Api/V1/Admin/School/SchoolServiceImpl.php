<?php

namespace App\Http\Services\Api\V1\Admin\School;

use App\Http\Resources\Api\V1\Admin\SchoolResource;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SchoolServiceImpl implements SchoolService
{
    public function index(Request $request)
    {
        $school = School::with('createdBy');

        // Sorting (secure)
        $sortableColumns = ['id', 'name', 'code', 'registration_number', 'created_at'];

        $sortBy = $request->get('sortBy', 'id');
        $sortDesc = $request->get('sortDesc', 'true') === 'true' ? 'desc' : 'asc';

        if (! in_array($sortBy, $sortableColumns)) {
            $sortBy = 'id';
        }

        $school->orderBy($sortBy, $sortDesc);

        // Search
        if ($search = $request->get('search')) {
            $school->where('name', 'like', "%{$search}%")
                ->orWhere('code', 'like', "%{$search}%")
                ->orWhere('registration_number', 'like', "%{$search}%");
        }

        // Pagination
        $itemsPerPage = (int) $request->get('itemsPerPage', 10);
        $schools = $school->paginate($itemsPerPage);

        return SchoolResource::collection($schools);
    }

    public function getAllSchools()
    {
        $schools = School::latest()->get();

        return SchoolResource::collection($schools);
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            
            $school = new School();

            $school->name = $request->name;
            $school->code = $request->code;
            $school->registration_number = $request->registration_number;
            $school->address = $request->address;
            $school->description = $request->description;
            $school->status = $request->status;
            $school->created_by = Auth::id() ? $request->created_by : null;

            $school->save();

            activity('School Store')
                ->performedOn($school)
                ->causedBy(Auth::user())
                ->withProperties(['attributes' => $request->all()])
                ->log('School Store Successful');

            DB::commit();

            return new SchoolResource($school);

        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }

    public function show(int $id)
    {
        $school = School::with('createdBy')->findOrFail($id);

        return new SchoolResource($school);
    }

    public function update(Request $request, int $id)
    {
        DB::beginTransaction();

        try {
            
            $school = School::with('createdBy')->findOrFail($id);

            $school->name = $request->name ?? $school->name;
            $school->code = $request->code ?? $school->code;
            $school->registration_number = $request->registration_number ?? $school->registration_number;
            $school->address = $request->address ?? $school->address;
            $school->description = $request->description ?? $school->description;
            $school->status = $request->status ?? 1;
            $school->created_by = Auth::id() ? $request->created_by : null;

            $school->save();

            activity('School Update')
                ->performedOn($school)
                ->causedBy(Auth::user())
                ->withProperties(['attributes' => $request->all()])
                ->log('School Update Successful');

            DB::commit();

            return new SchoolResource($school);

        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }

    public function destroy(int $id)
    {
        DB::beginTransaction();

        try {
            
            $school = School::findOrFail($id);

            $school->delete();

            activity('School Delete')
                ->performedOn($school)
                ->causedBy(Auth::user())
                ->withProperties(['attributes' => $school->toArray()])
                ->log('School Delete Successful');

            DB::commit();

            return true;

        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }

    public function changeStatus(int $id)
    {
        DB::beginTransaction();

        try {

            $school = School::findOrFail($id);

            $school->status = ! $school->status;

            $school->created_by = Auth::id() ?? null;

            $school->update();

            activity('School Status Update')
                ->performedOn($school)
                ->causedBy(Auth::user())
                ->withProperties(['id' => $id, 'status' => $school->status])
                ->log('School Status Update Successful');

            DB::commit();

            return new SchoolResource($school);

        } catch (\Throwable $th) {

            DB::rollBack();

            throw $th;
        }
    }
}