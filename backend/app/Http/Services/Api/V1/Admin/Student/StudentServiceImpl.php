<?php

namespace App\Http\Services\Api\V1\Admin\Student;

use App\Helper\ImageUpload;
use App\Http\Resources\Api\V1\Admin\StudentResource;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StudentServiceImpl implements StudentService
{
    public function index(Request $request)
    {
        $student = Student::with('school', 'schoolClass', 'medium', 'section', 'academic');

         // Sorting (secure)
        $sortableColumns = ['id', 'first_name', 'email', 'phone', 'created_at'];

        $sortBy = $request->get('sortBy', 'id');
        $sortDesc = $request->get('sortDesc', 'true') === 'true' ? 'desc' : 'asc';

        if (! in_array($sortBy, $sortableColumns)) {
            $sortBy = 'id';
        }

        $student->orderBy($sortBy, $sortDesc);

        // Search
        if ($search = $request->get('search')) {
            $student->where('first_name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('phone', 'like', "%{$search}%");
        }

        // Pagination
        $itemsPerPage = (int) $request->get('itemsPerPage', 10);
        $students = $student->paginate($itemsPerPage);

        return StudentResource::collection($students);
    }

    public function getAllStudents()
    {
        $students = Student::with('school', 'schoolClass', 'medium', 'section', 'academic')->latest()->get();

        return StudentResource::collection($students);
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            
            // Base data
            $data = $request->only([
                'school_id',
                'medium_id',
                'current_class_id',
                'current_section_id',
                'current_academic_year_id',
                'admission_number',
                'admission_date',

                'first_name',
                'middle_name',
                'last_name',
                'date_of_birth',
                'gender',
                'blood_group',
                'religion',
                'nationality',

                'email',
                'phone',
                'present_address',
                'permanent_address',

                'roll_number',
                'previous_school_name',
                'previous_class',

                'status',
                'birth_certificate_no',
            ]);

            /**
             * Auto-generate student_id
             * Example: STD-2026-00001
             */
            $lastId = Student::max('id') + 1;
            $data['student_id'] = 'STD-' . date('Y') . '-' . str_pad($lastId, 5, '0', STR_PAD_LEFT);

            /**
             * Profile Photo Upload
             */
            if ($request->hasFile('profile_photo')) {
                $photoPath = ImageUpload::uploadImageApplicationStorage(
                    $request->file('profile_photo'),
                    'students/profile-photos'
                );

                $data['profile_photo'] = $photoPath;
                $data['profile_photo_url'] = asset('storage/' . $photoPath);
            }

            /**
             * Birth Certificate Upload (if file)
             */
            if ($request->hasFile('birth_certificate_file')) {
                $certificatePath = ImageUpload::uploadImageApplicationStorage(
                    $request->file('birth_certificate_file'),
                    'students/birth-certificates'
                );

                $data['birth_certificate_no_url'] = asset('storage/' . $certificatePath);
            }

            /**
             * Audit fields
             */
            $data['created_by'] = Auth::id() ?? null;

            // Create student
            $student = Student::create($data);

            activity('Student Store')
                ->performedOn($student)
                ->causedBy(Auth::user())
                ->withProperties(['attributes' => $request->all()])
                ->log('Student Store Successful');

            DB::commit();

            return new StudentResource($student);

        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }

    public function show(int $id)
    {
        $student = Student::with('school', 'schoolClass', 'medium', 'section', 'academic')->findOrFail($id);

        return new StudentResource($student);
    }

    public function update(Request $request, int $id)
    {
        DB::beginTransaction();

        try {

            $student = Student::findOrFail($id);

            // Base update data (student_id কখনো update করা হবে না)
            $data = $request->only([
                'school_id',
                'medium_id',
                'current_class_id',
                'current_section_id',
                'current_academic_year_id',
                'admission_number',
                'admission_date',

                'first_name',
                'middle_name',
                'last_name',
                'date_of_birth',
                'gender',
                'blood_group',
                'religion',
                'nationality',

                'email',
                'phone',
                'present_address',
                'permanent_address',

                'roll_number',
                'previous_school_name',
                'previous_class',

                'status',
                'birth_certificate_no',
            ]);

            /**
             * Profile Photo Update
             */
            if ($request->hasFile('profile_photo')) {

                // Old photo delete (optional but recommended)
                if ($student->profile_photo) {
                    ImageUpload::deleteApplicationStorage($student->profile_photo);
                }

                $photoPath = ImageUpload::uploadImageApplicationStorage(
                    $request->file('profile_photo'),
                    'students/profile-photos'
                );

                $data['profile_photo'] = $photoPath;
                $data['profile_photo_url'] = asset('storage/' . $photoPath);
            }

            /**
             * Birth Certificate Update
             */
            if ($request->hasFile('birth_certificate_file')) {

                // Old photo delete (optional but recommended)
                if ($student->birth_certificate_file) {
                    ImageUpload::deleteApplicationStorage($student->birth_certificate_file);
                }

                $certificatePath = ImageUpload::uploadImageApplicationStorage(
                    $request->file('birth_certificate_file'),
                    'students/birth-certificates'
                );

                $data['birth_certificate_no_url'] = asset('storage/' . $certificatePath);
            }

            /**
             * Audit
             */
            $data['updated_by'] = Auth::id() ?? null;

            // Update
            $student->update($data);

            activity('Student update')
                ->performedOn($student)
                ->causedBy(Auth::user())
                ->withProperties(['attributes' => $request->all()])
                ->log('Student update Successful');

            DB::commit();

            return new StudentResource($student->fresh());

        } catch (\Throwable $th) {
            DB::rollBack();
            
            throw $th;
        }
    }

    public function destroy(int $id)
    {
        DB::beginTransaction();

        try {
            
            $student = Student::findOrFail($id);

            $student->delete();

            activity('Student delete')
                ->performedOn($student)
                ->causedBy(Auth::user())
                ->withProperties(['attributes' => $id])
                ->log('Student delete Successful');

            DB::commit();

            return true;

        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }

    public function changeStatus(Request $request, int $id)
    {
         DB::beginTransaction();

        try {

            $student = Student::findOrFail($id);

            /**
             * Validate status
             */
            $request->validate([
                'status' => [
                    'required',
                    'in:Active,Transferred,Dropout,TC_Issued,Completed',
                ],
            ]);

            /**
             * Update status
             */
            $student->update([
                'status'     => $request->status,
                'updated_by' => Auth::id() ?? null,
            ]);

            activity('Student status update')
                ->performedOn($student)
                ->causedBy(Auth::user())
                ->withProperties(['attributes' => $request->all(), 'id' => $id])
                ->log('Student status update Successful');

            DB::commit();

            return new StudentResource($student);
            
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
}