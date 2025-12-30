<?php

namespace App\Http\Services\Api\V1\Admin\Medium;

use App\Http\Resources\Api\V1\Admin\MediumResource;
use App\Models\Medium;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MediumServiceImpl implements MediumService
{
    public function index(Request $request)
    {

        $isActive = $request->get('is_active'); // 0 | 1 | null
        $search   = $request->get('search');    // string | null

        // 1 Call Stored Procedure
        $rows = DB::select(
            'CALL sp_get_mediums(?, ?)',
            [$isActive, $search]
        );

        // 2 Convert to Collection
        $collection = collect($rows);

        // 3 Sorting
        $sortableColumns = ['id', 'name', 'code', 'created_at'];
        $sortBy = $request->get('sortBy', 'id');
        $sortDesc = $request->get('sortDesc', 'true') === 'true';

        if (!in_array($sortBy, $sortableColumns)) {
            $sortBy = 'id';
        }

        $collection = $collection->sortBy($sortBy);

        if ($sortDesc) {
            $collection = $collection->reverse();
        }

        // 4 Manual Pagination
        $perPage = (int) $request->get('itemsPerPage', 10);
        $page    = (int) $request->get('page', 1);

        $paginator = new LengthAwarePaginator(
            $collection->forPage($page, $perPage)->values(),
            $collection->count(),
            $perPage,
            $page,
            [
                'path' => request()->url(),
                'query' => request()->query(),
            ]
        );

        // 5 Resource response
        return MediumResource::collection($paginator);
    }

    public function getAllActiveMedia()
    {
        $mediums = Medium::where('is_active', true)->get();

        return MediumResource::collection($mediums);
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            
            $medium = new Medium();
            
            $medium->name = $request->name;
            $medium->code = $request->code;
            $medium->description = $request->description;
            $medium->is_active = $request->is_active;

           
            $medium->save();
            

            activity('Medium Store')
                ->performedOn($medium)
                ->causedBy(Auth::user())
                ->withProperties(['attributes' => $request->all()])
                ->log('Medium Store Successful');

            DB::commit();

            return new MediumResource($medium);
            
        } catch (\Throwable $th) {

            DB::rollBack();

            throw $th;
        }
    }

    public function show(int $id)
    {
        $result = DB::selectOne(
            'CALL sp_get_medium_by_id(?)',
            [$id]
        );

        if (!$result) {
            abort(404, 'Medium not found');
        }

        return new MediumResource($result);
    }

    public function update(int $id, Request $request)
    {
        DB::beginTransaction();

        try {
            
            $medium = Medium::findOrFail($id);
            
            $medium->name = $request->name ?? $medium->name;
            $medium->code = $request->code ?? $medium->code;
            $medium->description = $request->description ?? $medium->description;

            if ($request->has('is_active')) {
                $medium->is_active = $request->is_active;
            }

            $medium->save();

            activity('Medium Update')
                ->performedOn($medium)
                ->causedBy(Auth::user())
                ->withProperties(['attributes' => $request->all()])
                ->log('Medium Update Successful');

            DB::commit();

            return new MediumResource($medium);
            
        } catch (\Throwable $th) {

            DB::rollBack();

            throw $th;
        }
    }

    public function destroy(int $id)
    {
        DB::beginTransaction();

        try {
            
            $medium = Medium::findOrFail($id);
            $medium->delete();

            activity('Medium Delete')
                ->performedOn($medium)
                ->causedBy(Auth::user())
                ->withProperties(['attributes' => ['id' => $id]])
                ->log('Medium Delete Successful');

            DB::commit();

            return response()->noContent();
            
        } catch (\Throwable $th) {

            DB::rollBack();

            throw $th;
        }
    }

    public function statusUpdate(int $id, bool $isActive)
    {
        DB::beginTransaction();

        try {
            
            $medium = Medium::findOrFail($id);
            $medium->is_active = $isActive;
            $medium->save();

            activity('Medium Status Update')
                ->performedOn($medium)
                ->causedBy(Auth::user())
                ->withProperties(['attributes' => ['id' => $id, 'is_active' => $isActive]])
                ->log('Medium Status Update Successful');

            DB::commit();

            return new MediumResource($medium);
            
        } catch (\Throwable $th) {

            DB::rollBack();

            throw $th;
        }
    }
}