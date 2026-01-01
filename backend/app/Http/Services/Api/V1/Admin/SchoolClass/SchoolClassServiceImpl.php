<?php

namespace App\Http\Services\Api\V1\Admin\SchoolClass;

use App\Http\Resources\Api\V1\Admin\SchoolClassResource;
use App\Models\SchoolClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SchoolClassServiceImpl implements SchoolClassService
{
    public function index(Request $request)
    {
        $schoolClasses = SchoolClass::with('medium');

         // Sorting (secure)
        $sortableColumns = ['id', 'name', 'code', 'order_number', 'created_at'];

        $sortBy = $request->get('sortBy', 'id');
        $sortDesc = $request->get('sortDesc', 'true') === 'true' ? 'desc' : 'asc';

        if (! in_array($sortBy, $sortableColumns)) {
            $sortBy = 'id';
        }

        $schoolClasses->orderBy($sortBy, $sortDesc);

        // Search
        if ($search = $request->get('search')) {
            $schoolClasses->where('name', 'like', "%{$search}%")
                ->orWhere('code', 'like', "%{$search}%")
                ->orWhere('order_number', 'like', "%{$search}%");
        }

        // Pagination
        $itemsPerPage = (int) $request->get('itemsPerPage', 10);
        $schoolClasses = $schoolClasses->paginate($itemsPerPage);

        return SchoolClassResource::collection($schoolClasses);
    }

    public function getAllClasses()
    {
        $schoolClasses = SchoolClass::with('medium')->latest()->get();

        return SchoolClassResource::collection($schoolClasses);
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {

            $schoolClasses = new SchoolClass();

            $schoolClasses->medium_id = $request->medium_id;
            $schoolClasses->name = $request->name;
            $schoolClasses->code = $request->code;
            $schoolClasses->order_number = $request->order_number;

            $schoolClasses->is_active = $request->has('is_active') ? $request->is_active : true;

            $schoolClasses->created_by = Auth::id() ?? null;

            $schoolClasses->save();

            activity('SchoolClass Store')
                ->performedOn($schoolClasses)
                ->causedBy(Auth::user())
                ->withProperties(['attributes' => $request->all()])
                ->log('SchoolClass Store Successful');

            DB::commit();

            return new SchoolClassResource($schoolClasses);

        } catch (\Throwable $th) {

            DB::rollBack();

            throw $th;
        }
    }

    public function show(int $id)
    {
        $schoolClasses = SchoolClass::with('medium')->findOrFail($id);

        return new SchoolClassResource($schoolClasses);
    }

    public function update(Request $request, int $id)
    {
        DB::beginTransaction();

        try {

            $schoolClasses = SchoolClass::findOrFail($id);

            if ($request->has('medium_id')) {
                $schoolClasses->medium_id = $request->medium_id;
            }
            if ($request->has('name')) {
                $schoolClasses->name = $request->name;
            }
            if ($request->has('code')) {
                $schoolClasses->code = $request->code;
            }
            if ($request->has('order_number')) {
                $schoolClasses->order_number = $request->order_number;
            }

            if ($request->has('is_active')) {
                $schoolClasses->is_active = $request->is_active;
            }

            $schoolClasses->created_by = Auth::id() ?? null;

            $schoolClasses->save();

            activity('SchoolClass Update')
                ->performedOn($schoolClasses)
                ->causedBy(Auth::user())
                ->withProperties(['attributes' => $request->all()])
                ->log('SchoolClass Update Successful');

            DB::commit();

            return new SchoolClassResource($schoolClasses);

        } catch (\Throwable $th) {

            DB::rollBack();

            throw $th;
        }
    }

    public function destroy(int $id)
    {
        DB::beginTransaction();

        try {

            $schoolClasses = SchoolClass::findOrFail($id);

            $schoolClasses->delete();

            activity('SchoolClass Delete')
                ->performedOn($schoolClasses)
                ->causedBy(Auth::user())
                ->withProperties(['id' => $id])
                ->log('SchoolClass Delete Successful');

            DB::commit();

            return true;

        } catch (\Throwable $th) {

            DB::rollBack();

            throw $th;
        }
    }

    public function statusUpdate(int $id)
    {
        DB::beginTransaction();

        try {

            $schoolClasses = SchoolClass::findOrFail($id);

            $schoolClasses->is_active = ! $schoolClasses->is_active;

            $schoolClasses->created_by = Auth::id() ?? null;

            $schoolClasses->save();

            activity('SchoolClass Status Update')
                ->performedOn($schoolClasses)
                ->causedBy(Auth::user())
                ->withProperties(['id' => $id, 'is_active' => $schoolClasses->is_active])
                ->log('SchoolClass Status Update Successful');

            DB::commit();

            return new SchoolClassResource($schoolClasses);

        } catch (\Throwable $th) {

            DB::rollBack();

            throw $th;
        }
    }
}