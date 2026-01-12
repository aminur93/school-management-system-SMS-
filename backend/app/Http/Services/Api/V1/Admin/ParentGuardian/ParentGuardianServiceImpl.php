<?php

namespace App\Http\Services\Api\V1\Admin\ParentGuardian;

use App\Http\Resources\Api\V1\Admin\ParentGuardianResource;
use App\Models\ParentGuardian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ParentGuardianServiceImpl implements ParentGuardianService
{
    public function index(Request $request)
    {
        $parent_guardian = ParentGuardian::with('student', 'createdBy');

        // Sorting (secure)
        $sortableColumns = ['id', 'first_name', 'middle_name', 'last_name', 'nid_number', 'email', 'phone', 'date_of_birth', 'created_at'];

        $sortBy = $request->get('sortBy', 'id');
        $sortDesc = $request->get('sortDesc', 'true') === 'true' ? 'desc' : 'asc';

        if (! in_array($sortBy, $sortableColumns)) {
            $sortBy = 'id';
        }

        $parent_guardian->orderBy($sortBy, $sortDesc);

        // Search
        if ($search = $request->get('search')) {
            $parent_guardian->where('first_name', 'like', "%{$search}%")
                ->orWhere('middle_name', 'like', "%{$search}%")
                ->orWhere('last_name', 'like', "%{$search}%")
                ->orWhere('nid_number', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('phone', 'like', "%{$search}%")
                ->orWhere('date_of_birth', 'like', "%{$search}%");
        }

        // Pagination
        $itemsPerPage = (int) $request->get('itemsPerPage', 10);
        $parent_guardians = $parent_guardian->paginate($itemsPerPage);

        return ParentGuardianResource::collection($parent_guardians);
    }

    public function getAllParentGuardians()
    {
        $parent_guardians = ParentGuardian::with('student', 'createdBy')->latest()->get();

        return ParentGuardianResource::collection($parent_guardians);
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            
            $parentGuardian = new ParentGuardian();

            // Foreign key
            $parentGuardian->student_id = $request->student_id;

            // Relation info
            $parentGuardian->relation_type = $request->relation_type;
            $parentGuardian->is_primary = $request->is_primary ?? false;

            // Personal Information
            $parentGuardian->first_name = $request->first_name;
            $parentGuardian->middle_name = $request->middle_name;
            $parentGuardian->last_name = $request->last_name;
            $parentGuardian->nid_number = $request->nid_number;
            $parentGuardian->date_of_birth = $request->date_of_birth;

            // Contact Information
            $parentGuardian->email = $request->email;
            $parentGuardian->phone = $request->phone;
            $parentGuardian->alternate_phone = $request->alternate_phone;
            $parentGuardian->address = $request->address;

            // Professional Information
            $parentGuardian->occupation = $request->occupation;
            $parentGuardian->organization = $request->organization;
            $parentGuardian->designation = $request->designation;
            $parentGuardian->annual_income = $request->annual_income;
            $parentGuardian->office_address = $request->office_address;

            // Emergency Contact
            $parentGuardian->is_emergency_contact = $request->is_emergency_contact ?? false;

            // Documents
            $parentGuardian->photo = $request->photo;
            $parentGuardian->nid_photo = $request->nid_photo;

            // Audit
            $parentGuardian->created_by = Auth::id() ?? null;

            /**
             * Business Rule:
             * One primary guardian per student
             */
            if ($parentGuardian->is_primary) {
                ParentGuardian::where('student_id', $request->student_id)
                    ->update(['is_primary' => false]);
            }

            /**
             * Business Rule:
             * One emergency contact per student
             */
            if ($parentGuardian->is_emergency_contact) {
                ParentGuardian::where('student_id', $request->student_id)
                    ->update(['is_emergency_contact' => false]);
            }

            $parentGuardian->save();

            activity('Parent guardian store')
                ->performedOn($parentGuardian)
                ->causedBy(Auth::user())
                ->withProperties(['attributes' => $request->all()])
                ->log('Parent/Guardian store successful');

            DB::commit();

            return new ParentGuardianResource($parentGuardian);

        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }

    public function show(int $id)
    {
        $parent_guardian = ParentGuardian::with('student', 'createdBy')->findOrFail($id);

        return new ParentGuardianResource($parent_guardian);
    }

    public function update(Request $request, int $id)
    {
        DB::beginTransaction();

        try {

            $parentGuardian = ParentGuardian::findOrFail($id);

            // Foreign key
            if ($request->has('student_id')) {
                $parentGuardian->student_id = $request->student_id;
            }

            // Relation info
            if ($request->has('relation_type')) {
                $parentGuardian->relation_type = $request->relation_type;
            }

            if ($request->has('is_primary')) {
                $parentGuardian->is_primary = $request->is_primary;
            }

            // Personal Information
            if ($request->has('first_name')) {
                $parentGuardian->first_name = $request->first_name;
            }

            if ($request->has('middle_name')) {
                $parentGuardian->middle_name = $request->middle_name;
            }

            if ($request->has('last_name')) {
                $parentGuardian->last_name = $request->last_name;
            }

            if ($request->has('nid_number')) {
                $parentGuardian->nid_number = $request->nid_number;
            }

            if ($request->has('date_of_birth')) {
                $parentGuardian->date_of_birth = $request->date_of_birth;
            }

            // Contact Information
            if ($request->has('email')) {
                $parentGuardian->email = $request->email;
            }

            if ($request->has('phone')) {
                $parentGuardian->phone = $request->phone;
            }

            if ($request->has('alternate_phone')) {
                $parentGuardian->alternate_phone = $request->alternate_phone;
            }

            if ($request->has('address')) {
                $parentGuardian->address = $request->address;
            }

            // Professional Information
            if ($request->has('occupation')) {
                $parentGuardian->occupation = $request->occupation;
            }

            if ($request->has('organization')) {
                $parentGuardian->organization = $request->organization;
            }

            if ($request->has('designation')) {
                $parentGuardian->designation = $request->designation;
            }

            if ($request->has('annual_income')) {
                $parentGuardian->annual_income = $request->annual_income;
            }

            if ($request->has('office_address')) {
                $parentGuardian->office_address = $request->office_address;
            }

            // Emergency Contact
            if ($request->has('is_emergency_contact')) {
                $parentGuardian->is_emergency_contact = $request->is_emergency_contact;
            }

            // Documents
            if ($request->has('photo')) {
                $parentGuardian->photo = $request->photo;
            }

            if ($request->has('nid_photo')) {
                $parentGuardian->nid_photo = $request->nid_photo;
            }

            // Audit
            $parentGuardian->created_by = Auth::id() ?? null;

            /**
             * Business Rule:
             * One primary guardian per student
             */
            if ($request->has('is_primary') && $request->is_primary) {
                ParentGuardian::where('student_id', $parentGuardian->student_id)
                    ->where('id', '!=', $parentGuardian->id)
                    ->update(['is_primary' => false]);
            }

            /**
             * Business Rule:
             * One emergency contact per student
             */
            if ($request->has('is_emergency_contact') && $request->is_emergency_contact) {
                ParentGuardian::where('student_id', $parentGuardian->student_id)
                    ->where('id', '!=', $parentGuardian->id)
                    ->update(['is_emergency_contact' => false]);
            }

            $parentGuardian->save();

            activity('Parent guardian update')
                ->performedOn($parentGuardian)
                ->causedBy(Auth::user())
                ->withProperties([
                    'attributes' => $request->all(),
                    'old' => $parentGuardian->getOriginal(),
                ])
                ->log('Parent/Guardian update successful');

            DB::commit();

            return new ParentGuardianResource($parentGuardian);

        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    public function destroy(int $id)
    {
        DB::beginTransaction();

        try {
            
            $parentGuardian = ParentGuardian::findOrFail($id);

            $parentGuardian->delete();

            DB::commit();

            return true;

        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }
}