<?php

namespace App\Http\Services\Api\V1\Admin\StudentEnrollment;

use App\Http\Resources\Api\V1\Admin\StudentEnrollmentResource;
use App\Models\StudentEnrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StudentEnrollmentServiceImpl implements StudentEnrollmentService
{
    public function index(Request $request)
    {
        $student_enrollment = StudentEnrollment::with('student', 'academicYear', 'schoolClass', 'section', 'createdBy');

         // Sorting (secure)
        $sortableColumns = ['id', 'roll_number', 'enrollment_date', 'enrollment_status', 'created_at'];

        $sortBy = $request->get('sortBy', 'id');
        $sortDesc = $request->get('sortDesc', 'true') === 'true' ? 'desc' : 'asc';

        if (! in_array($sortBy, $sortableColumns)) {
            $sortBy = 'id';
        }

        $student_enrollment->orderBy($sortBy, $sortDesc);

        // Search
        if ($search = $request->get('search')) {
            $student_enrollment->where('roll_number', 'like', "%{$search}%")
                ->orWhere('enrollment_date', 'like', "%{$search}%")
                ->orWhere('enrollment_status', 'like', "%{$search}%");
        }

        // Pagination
        $itemsPerPage = (int) $request->get('itemsPerPage', 10);
        $student_enrollments = $student_enrollment->paginate($itemsPerPage);

        return StudentEnrollmentResource::collection($student_enrollments);
    }

    public function getAllStudentEnrollments()
    {
        $student_enrollment = StudentEnrollment::with('student', 'academicYear', 'schoolClass', 'section', 'createdBy')->latest()->get();

        return StudentEnrollmentResource::collection($student_enrollment);
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            
            // Auto calculate net fees (if values provided)
            $totalFees = $request->total_fees ?? 0;
            $discount = $request->discount_amount ?? 0;
            $scholarship = $request->scholarship_amount ?? 0;

            $netFees = $totalFees - ($discount + $scholarship);

            $studentEnrollment = new StudentEnrollment();

            $studentEnrollment->student_id = $request->student_id;
            $studentEnrollment->academic_year_id = $request->academic_year_id;
            $studentEnrollment->school_class_id = $request->school_class_id;
            $studentEnrollment->section_id = $request->section_id;
            $studentEnrollment->roll_number = $request->roll_number;
            $studentEnrollment->enrollment_date = $request->enrollment_date;
            $studentEnrollment->enrollment_status = $request->enrollment_status ?? 'Enrolled';

            $studentEnrollment->total_fees = $request->total_fees;
            $studentEnrollment->discount_amount = $request->discount_amount;
            $studentEnrollment->scholarship_amount = $request->scholarship_amount;
            $studentEnrollment->net_fees = $netFees;

            $studentEnrollment->remarks = $request->remarks;

            $studentEnrollment->save();

            activity('Student Enrollment store')
                ->performedOn($studentEnrollment)
                ->causedBy(Auth::user())
                ->withProperties(['attributes' => $request->all()])
                ->log('Student Enrollment store successful');

            DB::commit();

            return new StudentEnrollmentResource($studentEnrollment);
            
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }

    public function show(int $id)
    {
        $student_enrollment = StudentEnrollment::with('student', 'academicYear', 'schoolClass', 'section', 'createdBy')->findOrFail($id);

        return new StudentEnrollmentResource($student_enrollment);
    }

    public function update(Request $request, int $id)
    {
        DB::beginTransaction();

        try {

            $studentEnrollment = StudentEnrollment::findOrFail($id);

            // Auto calculate net fees (if values provided)
            $totalFees = $request->has('total_fees')
                ? $request->total_fees
                : ($studentEnrollment->total_fees ?? 0);

            $discount = $request->has('discount_amount')
                ? $request->discount_amount
                : ($studentEnrollment->discount_amount ?? 0);

            $scholarship = $request->has('scholarship_amount')
                ? $request->scholarship_amount
                : ($studentEnrollment->scholarship_amount ?? 0);

            $netFees = $totalFees - ($discount + $scholarship);

            // Update fields (only if present)
            $studentEnrollment->student_id = $request->student_id ?? $studentEnrollment->student_id;
            $studentEnrollment->academic_year_id = $request->academic_year_id ?? $studentEnrollment->academic_year_id;
            $studentEnrollment->school_class_id = $request->school_class_id ?? $studentEnrollment->school_class_id;
            $studentEnrollment->section_id = $request->section_id ?? $studentEnrollment->section_id;
            $studentEnrollment->roll_number = $request->roll_number ?? $studentEnrollment->roll_number;
            $studentEnrollment->enrollment_date = $request->enrollment_date ?? $studentEnrollment->enrollment_date;
            $studentEnrollment->enrollment_status = $request->enrollment_status ?? $studentEnrollment->enrollment_status;

            $studentEnrollment->total_fees = $totalFees;
            $studentEnrollment->discount_amount = $discount;
            $studentEnrollment->scholarship_amount = $scholarship;
            $studentEnrollment->net_fees = $netFees;

            $studentEnrollment->remarks = $request->remarks ?? $studentEnrollment->remarks;

            $studentEnrollment->save();

            activity('Student Enrollment update')
                ->performedOn($studentEnrollment)
                ->causedBy(Auth::user())
                ->withProperties([
                    'old' => $studentEnrollment->getOriginal(),
                    'new' => $request->all(),
                ])
                ->log('Student Enrollment update successful');

            DB::commit();

            return new StudentEnrollmentResource($studentEnrollment);

        } catch (\Throwable $th) {

            DB::rollBack();
            
            throw $th;
        }
    }

    public function destroy(int $id)
    {
        DB::beginTransaction();

        try {
            
            $studentEnrollment = StudentEnrollment::findOrFail($id);

            $oldData = $studentEnrollment->toArray();

            $studentEnrollment->delete();

            activity('Student Enrollment delete')
                ->performedOn($studentEnrollment)
                ->causedBy(Auth::user())
                ->withProperties([
                    'old' => $oldData
                ])
                ->log('Student Enrollment deleted successfully');

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

            $studentEnrollment = StudentEnrollment::findOrFail($id);

            $oldStatus = $studentEnrollment->enrollment_status;

            $studentEnrollment->enrollment_status = $request->enrollment_status;
            $studentEnrollment->save();

            activity('Student Enrollment status change')
                ->performedOn($studentEnrollment)
                ->causedBy(Auth::user())
                ->withProperties([
                    'old_status' => $oldStatus,
                    'new_status' => $request->enrollment_status,
                ])
                ->log('Student Enrollment status changed');

            DB::commit();

            return new StudentEnrollmentResource($studentEnrollment);

        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
}