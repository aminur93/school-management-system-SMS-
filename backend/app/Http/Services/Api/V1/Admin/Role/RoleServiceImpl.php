<?php

namespace App\Http\Services\Api\V1\Admin\Role;

use App\Http\Resources\Api\V1\Admin\RoleResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class RoleServiceImpl implements RoleService
{


    /**
     * @inheritDoc
     */
    public function destroy(int $id) {

        $role = Role::findOrFail($id);

        $role->permissions()->detach();

        $role->delete();

        activity('Role Destroy')
            ->performedOn($role)
            ->causedBy(Auth::user())
            ->withProperties(['attributes' => $role->toArray()])
            ->log('Role Destroy Successful');
    }

    /**
     * @inheritDoc
     */
    public function getAllRoles() {

        $roles = Role::with('permissions')->latest()->get();

        return RoleResource::collection($roles);
    }

    /**
     * @inheritDoc
     */
    public function index(Request $request) {

        $roles = Role::with('permissions');

         // Sorting (secure)
        $sortableColumns = ['id', 'name', 'level', 'created_at'];

        $sortBy = $request->get('sortBy', 'id');
        $sortDesc = $request->get('sortDesc', 'true') === 'true' ? 'desc' : 'asc';

        if (! in_array($sortBy, $sortableColumns)) {
            $sortBy = 'id';
        }

        $roles->orderBy($sortBy, $sortDesc);

        // Search
        if ($search = $request->get('search')) {
            $roles->where('name', 'like', "%{$search}%")
                ->orWhere('level', 'like', "%{$search}%");
        }

        // Pagination
        $itemsPerPage = (int) $request->get('itemsPerPage', 10);
        $roles = $roles->paginate($itemsPerPage);

        return RoleResource::collection($roles);
    }

    /**
     * @inheritDoc
     */
    public function show(int $id) {
        $role = Role::findOrFail($id);

        return new RoleResource($role);
    }

    /**
     * @inheritDoc
     */
    public function store(Request $request) {

        DB::beginTransaction();

        try {
            
            $role = new Role();

            $role->name = $request->name;
            $role->level = $request->level;
            $role->syncPermissions($request->permissions);

            $role->save();

            activity('Role Store')
                ->performedOn($role)
                ->causedBy(Auth::user())
                ->withProperties(['attributes' => $request->all()])
                ->log('Role Store Successful');

            DB::commit();

            return new RoleResource($role);

        } catch (\Throwable $th) {

            DB::rollBack();

            throw $th;
        }
    }

    /**
     * @inheritDoc
     */
    public function update(int $id, Request $request) {

        DB::beginTransaction();

        try {
            
            $role = Role::findOrFail($id);

            $role->name = $request->name ?? $role->name;
            $role->level = $request->level ?? $role->level;

            if($request->has('permissions'))
            {
                $role->syncPermissions($request->permissions);
            }
            
            $role->update();

            activity('Role Update')
                ->performedOn($role)
                ->causedBy(Auth::user())
                ->withProperties(['attributes' => $request->all()])
                ->log('Role Update Successful');

            DB::commit();

            return new RoleResource($role);

        } catch (\Throwable $th) {

            DB::rollBack();

            throw $th;
        }
    }
}