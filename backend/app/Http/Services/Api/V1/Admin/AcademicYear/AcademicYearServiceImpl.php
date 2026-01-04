<?php

namespace App\Http\Services\Api\V1\Admin\AcademicYear;

use App\Http\Resources\Api\V1\Admin\AcademicYearResource;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AcademicYearServiceImpl implements AcademicYearService
{
    public function index(Request $request)
    {
        $academic_year = AcademicYear::with('createdBy');

         // Sorting (secure)
        $sortableColumns = ['id', 'year_name', 'start_date', 'end_date', 'created_at'];

        $sortBy = $request->get('sortBy', 'id');
        $sortDesc = $request->get('sortDesc', 'true') === 'true' ? 'desc' : 'asc';

        if (! in_array($sortBy, $sortableColumns)) {
            $sortBy = 'id';
        }

        $academic_year->orderBy($sortBy, $sortDesc);

        // Search
        if ($search = $request->get('search')) {
            $academic_year->where('year_name', 'like', "%{$search}%")
                ->orWhere('start_date', 'like', "%{$search}%")
                ->orWhere('end_date', 'like', "%{$search}%");
        }

        // Pagination
        $itemsPerPage = (int) $request->get('itemsPerPage', 10);
        $academic_years = $academic_year->paginate($itemsPerPage);

        return AcademicYearResource::collection($academic_years);
    }

    public function getAllAcademicYears()
    {
        $academic_years = AcademicYear::with('createdBy')->latest()->get();

        return AcademicYearResource::collection($academic_years);
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {

            $academic_year = new AcademicYear();

            $academic_year->year_name = $request->year_name;
            $academic_year->start_date = $request->start_date;
            $academic_year->end_date = $request->end_date;
            $academic_year->is_current = $request->is_current;
            $academic_year->is_active = $request->is_active;

            $academic_year->created_by = Auth::id() ? $request->created_by : null;

            $academic_year->save();

            activity('Academic year store')
                ->performedOn($academic_year)
                ->causedBy(Auth::user())
                ->withProperties(['attributes' => $request->all()])
                ->log('Academic year store Successful');

            DB::commit();

            return new AcademicYearResource($academic_year);
            
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }

    public function show(int $id)
    {
        $academic_year = AcademicYear::with('createdBy')->findOrFail($id);

        return new AcademicYearResource($academic_year);
    }

    public function update(Request $request, int $id)
    {
        DB::beginTransaction();

        try {

            $academic_year = AcademicYear::findOrFail($id);

            $academic_year->year_name = $request->year_name ?? $academic_year->year_name;
            $academic_year->start_date = $request->start_date ?? $academic_year->start_date;
            $academic_year->end_date = $request->end_date ?? $academic_year->end_date;
            $academic_year->is_current = $request->is_current ?? $academic_year->is_current;
            $academic_year->is_active = $request->is_active ?? $academic_year->is_active;

            $academic_year->created_by = Auth::id() ? $request->created_by : null;

            $academic_year->save();

            activity('Academic year update')
                ->performedOn($academic_year)
                ->causedBy(Auth::user())
                ->withProperties(['attributes' => $request->all()])
                ->log('Academic year update Successful');

            DB::commit();

            return new AcademicYearResource($academic_year);
            
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }

    public function destroy(int $id)
    {
        DB::beginTransaction();

        try {
            $academic_year = AcademicYear::findOrFail($id);
            $academic_year->delete();

            activity('Academic year Delete')
                ->performedOn($academic_year)
                ->causedBy(Auth::user())
                ->withProperties(['attributes' => ['id' => $id]])
                ->log('Academic year Delete Successful');

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

            $academic_year = AcademicYear::findOrFail($id);

            $academic_year->is_active = ! $academic_year->is_active;

            $academic_year->created_by = Auth::id() ?? null;

            $academic_year->save();

            activity('Academic year Status Update')
                ->performedOn($academic_year)
                ->causedBy(Auth::user())
                ->withProperties(['id' => $id, 'is_active' => $academic_year->is_active])
                ->log('Academic year Status Update Successful');

            DB::commit();

            return new AcademicYearResource($academic_year);

        } catch (\Throwable $th) {

            DB::rollBack();

            throw $th;
        }
    }
}