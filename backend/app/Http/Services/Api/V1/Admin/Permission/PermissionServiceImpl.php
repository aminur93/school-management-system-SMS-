<?php

namespace App\Http\Services\Api\V1\Admin\Permission;

use App\Http\Resources\Api\V1\Admin\PermissionResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;

class PermissionServiceImpl implements PermissionService
{
	public function index(Request $request)
	{
		$query = (new Permission)
        ->setConnection('mysql_read_2')
        ->newQuery();

        $sortableColumns = ['id', 'name', 'title', 'created_at'];

        $sortBy = in_array($request->get('sortBy'), $sortableColumns)
            ? $request->get('sortBy')
            : 'id';

        $sortDesc = $request->get('sortDesc') === 'true' ? 'desc' : 'asc';

        $query->orderBy($sortBy, $sortDesc);

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                ->orWhere('title', 'like', "%{$search}%");
            });
        }

        $permissions = $query->paginate(
            (int) $request->get('itemsPerPage', 10)
        );

        return PermissionResource::collection($permissions);
	}

	public function getAllPermissions()
	{
		$permissions = Permission::orderBy('id', 'desc')->get(); // keep as Collection
        return PermissionResource::collection($permissions);
	}

	public function store(Request $request)
	{
		DB::beginTransaction();

        try {
            
            $permission = new Permission();

            $permission->name = $request->name;
            $permission->title = $request->title;

            $permission->save();

            activity('Permission Store')
                ->performedOn($permission)
                ->causedBy(Auth::user())
                ->withProperties(['attributes' => $request->all()])
                ->log('Permission Store Successful');

            DB::commit();

           
            return new PermissionResource($permission);

        } catch (\Throwable $th) {

            DB::rollBack();

            throw $th;
        }
	}

	public function show($id)
	{
		$permission = Permission::findOrFail($id);

        return new PermissionResource($permission);
	}

	public function update($id, Request $request)
	{
		DB::beginTransaction();

        try {
            
            $permission = Permission::findOrFail($id);

            $permission->name = $request->name ?? $permission->name;
            $permission->title = $request->title ?? $permission->title;

            $permission->save();

            activity('Permission Update')
                ->performedOn($permission)
                ->causedBy(Auth::user())
                ->withProperties(['attributes' => $request->all()])
                ->log('Permission Update Successful');

            DB::commit();

            return new PermissionResource($permission);

        } catch (\Throwable $th) {

            DB::rollBack();

            throw $th;
        }
	}

	public function destroy($id)
	{
		$permission = Permission::findOrFail($id);

         activity('Permission delete')
            ->performedOn($permission)
            ->causedBy(Auth::user())
            ->withProperties(['attributes' => $id])
            ->log('Permission delete Successful');

        $permission->delete();
	}
}