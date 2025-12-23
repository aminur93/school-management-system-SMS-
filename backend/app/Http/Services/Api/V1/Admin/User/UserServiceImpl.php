<?php

namespace App\Http\Services\Api\V1\Admin\User;

use App\Helper\ImageUpload;
use App\Http\Resources\Api\V1\Admin\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserServiceImpl implements UserService
{
    // Implement all methods defined in UserService interface

    public function index(Request $request)
    {
        $users = User::with('roles');

         // Sorting (secure)
        $sortableColumns = ['id', 'name', 'email', 'phone', 'created_at'];

        $sortBy = $request->get('sortBy', 'id');
        $sortDesc = $request->get('sortDesc', 'true') === 'true' ? 'desc' : 'asc';

        if (! in_array($sortBy, $sortableColumns)) {
            $sortBy = 'id';
        }

        $users->orderBy($sortBy, $sortDesc);

        // Search
        if ($search = $request->get('search')) {
            $users->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('phone', 'like', "%{$search}%");
        }

        // Pagination
        $itemsPerPage = (int) $request->get('itemsPerPage', 10);
        $users = $users->paginate($itemsPerPage);

        return UserResource::collection($users);
    }

    public function getAllUsers()
    {
        $users = User::with('roles')->latest()->get();

        return UserResource::collection($users);
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            
            $user = new User();

            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->user_type = $request->user_type;
            $user->password = Hash::make($request->password);

            $role = Role::findOrFail($request->role_id);
            $user->assignRole($role);

            if ($request->hasFile('image')) {

                $imagePath = ImageUpload::uploadImageApplicationStorage(
                    $request->file('image'),
                    'user-image'
                );

                // DB columns
                $user->image = $imagePath;
                $user->image_url = asset('storage/' . $imagePath);
            }

            $user->save();

            activity('User Store')
                ->performedOn($user)
                ->causedBy($user)
                ->withProperties(['attributes' => $request->all()])
                ->log('User Store Successful');

            DB::commit();

            return new UserResource($user);

        } catch (\Throwable $th) {

            DB::rollBack();

            throw $th;
        }
    }

    public function show(int $id)
    {
        $user = User::with('roles')->findOrFail($id);

        return new UserResource($user);
    }

    public function update(Request $request, int $id)
    {
         DB::beginTransaction();

        try {

            $user = User::findOrFail($id);

            $user->fill(
                $request->only([
                    'name',
                    'email',
                    'phone',
                    'user_type',
                ])
            );

            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
            }

            if ($request->has('role_id')) {
                $role = Role::findOrFail($request->role_id);
                $user->syncRoles([$role]);
            }

            if ($request->hasFile('image')) {
                $imagePath = ImageUpload::uploadImageApplicationStorage(
                    $request->file('image'),
                    'user-image'
                );

                $user->image = $imagePath;
                $user->image_url = asset('storage/' . $imagePath);
            }


            $user->save();

            activity('User Update')
                ->performedOn($user)
                ->causedBy($user)
                ->withProperties(['attributes' => $request->all()])
                ->log('User Update Successful');

            DB::commit();

            return new UserResource($user);

        } catch (\Throwable $th) {

            DB::rollBack();

            throw $th;
        }
    }

    public function destroy(int $id)
    {
        DB::beginTransaction();

        try {
            
            $user = User::findOrFail($id);
            $user->roles()->detach();
            $user->delete();

            activity('User Delete')
                ->performedOn($user)
                ->causedBy($user)
                ->withProperties(['attributes' => $user->toArray()])
                ->log('User Delete Successful');

            DB::commit();

            return true;

        } catch (\Throwable $th) {

            DB::rollBack();

            throw $th;
        }
    }
}