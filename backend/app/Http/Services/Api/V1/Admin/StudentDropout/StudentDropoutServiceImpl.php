<?php

namespace App\Http\Services\Api\V1\Admin\StudentDropout;

use App\Http\Resources\Api\V1\Admin\StudentDropoutResource;
use App\Http\Services\Api\V1\Admin\Student\StudentService;
use App\Models\StudentDropout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StudentDropoutServiceImpl implements StudentDropoutService
{
    protected StudentService $studentService;

    public function __construct(StudentService $studentService)
    {
        $this->studentService = $studentService;
    }

    public function index(Request $request)
    {
        $student_dropout = StudentDropout::with('student', 'academicYear', 'schoolClass', 'section', 'createdBy', 'updatedBy');

        // Sorting (secure)
        $sortableColumns = ['id', 'dropout_date', 'reason', 'created_at'];

        $sortBy = $request->get('sortBy', 'id');
        $sortDesc = $request->get('sortDesc', 'true') === 'true' ? 'desc' : 'asc';

        if (! in_array($sortBy, $sortableColumns)) {
            $sortBy = 'id';
        }

        $student_dropout->orderBy($sortBy, $sortDesc);

        // Search
        if ($search = $request->get('search')) {

             $student_dropout->where(function ($q) use ($search) {

                // Promotion Fields
                $q->where('dropout_date', 'ILIKE', "%{$search}%")
                ->orWhere('reason', 'ILIKE', "%{$search}%");

                // Student
                $q->orWhereHas('student', function ($q) use ($search) {
                    $q->where('name', 'ILIKE', "%{$search}%")
                    ->orWhere('roll_number', 'ILIKE', "%{$search}%")
                    ->orWhere('registration_number', 'ILIKE', "%{$search}%");
                });

                // From Academic Year
                $q->orWhereHas('academicYear', function ($q) use ($search) {
                    $q->where('name', 'ILIKE', "%{$search}%")
                    ->orWhere('year', 'ILIKE', "%{$search}%");
                });

                // From Class
                $q->orWhereHas('schoolClass', function ($q) use ($search) {
                    $q->where('name', 'ILIKE', "%{$search}%");
                });

                // From Section
                $q->orWhereHas('section', function ($q) use ($search) {
                    $q->where('name', 'ILIKE', "%{$search}%");
                });
            });
        }

        // Pagination
        $itemsPerPage = (int) $request->get('itemsPerPage', 10);
        $student_dropouts = $student_dropout->paginate($itemsPerPage);

        return StudentDropoutResource::collection($student_dropouts);
    }

    public function getAllStudentDropouts()
    {
        $student_dropouts = StudentDropout::with('student', 'academicYear', 'schoolClass', 'section', 'createdBy', 'updatedBy')->latest()->get();

        return StudentDropoutResource::collection($student_dropouts);
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            
             $dropout = new StudentDropout();

            // Student
            $dropout->student_id = $request->student_id;

            // Academic Information
            $dropout->academic_year_id = $request->academic_year_id;
            $dropout->school_class_id = $request->school_class_id;
            $dropout->section_id = $request->section_id;

            // Dropout Details
            $dropout->dropout_date = $request->dropout_date;
            $dropout->reason = $request->reason;
            $dropout->reason_details = $request->reason_details;

            // Academic Status
            $dropout->last_attendance_date = $request->last_attendance_date;
            $dropout->total_working_days = $request->total_working_days;
            $dropout->total_present_days = $request->total_present_days;
            $dropout->attendance_percentage = $request->attendance_percentage;

            // Fees Status
            $dropout->fees_due = $request->fees_due;
            $dropout->fees_cleared = $request->fees_cleared ?? false;

            // Follow-up
            $dropout->contacted_for_return = $request->contacted_for_return ?? false;
            $dropout->contact_date = $request->contact_date;
            $dropout->willing_to_return = $request->willing_to_return;

            // Other
            $dropout->remarks = $request->remarks;

            // Audit
            $dropout->created_by = Auth::id() ?? null;

            $dropout->save();

            if ($dropout) {

                $requestData = new Request([
                    'status' => 'Dropout'
                ]);
                
                $this->studentService->changeStatus($requestData, $request->student_id);
            }

            // Activity Log
            activity('Student Dropout')
                ->performedOn($dropout)
                ->causedBy(Auth::user())
                ->withProperties([
                    'student_id' => $dropout->student_id,
                    'reason' => $dropout->reason,
                    'dropout_date' => $dropout->dropout_date,
                ])
                ->log('Student dropout record created');

            DB::commit();

            return new StudentDropoutResource(
                $dropout->load('student', 'academicYear', 'schoolClass', 'section')
            );

        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }

    public function show(int $id)
    {
        $student_dropout = StudentDropout::with('student', 'academicYear', 'schoolClass', 'section', 'createdBy', 'updatedBy')->findOrFail($id);

        return new StudentDropoutResource($student_dropout);
    }

    public function update(Request $request, int $id)
    {
        DB::beginTransaction();

        try {
            
            $dropout = StudentDropout::findOrFail($id);

            // Student
            $dropout->student_id = $request->student_id ?? $dropout->student_id;

            // Academic Information
            $dropout->academic_year_id = $request->academic_year_id ?? $dropout->academic_year_id;
            $dropout->school_class_id = $request->class_id ?? $dropout->school_class_id;
            $dropout->section_id = $request->section_id ?? $dropout->section_id;

            // Dropout Details
            $dropout->dropout_date = $request->dropout_date ?? $dropout->dropout_date;
            $dropout->reason = $request->reason ?? $dropout->reason;
            $dropout->reason_details = $request->reason_details ?? $dropout->reason_details;

            // Academic Status
            $dropout->last_attendance_date = $request->last_attendance_date ?? $dropout->last_attendance_date;
            $dropout->total_working_days = $request->total_working_days ?? $dropout->total_working_days;
            $dropout->total_present_days = $request->total_present_days ?? $dropout->total_present_days;
            $dropout->attendance_percentage = $request->attendance_percentage ?? $dropout->attendance_percentage;

            // Fees Status
            $dropout->fees_due = $request->fees_due ?? $dropout->fees_due;
            $dropout->fees_cleared = $request->fees_cleared ?? $dropout->fees_cleared;

            // Follow-up
            $dropout->contacted_for_return = $request->contacted_for_return ?? $dropout->contacted_for_return;
            $dropout->contact_date = $request->contact_date ?? $dropout->contact_date;
            $dropout->willing_to_return = $request->willing_to_return ?? $dropout->willing_to_return;

            // Other
            $dropout->remarks = $request->remarks ?? $dropout->remarks;

            // Optional: updated_by (if column exists)
            $dropout->updated_by = Auth::id() ?? null;

            $dropout->save();

            // Activity Log
            activity('Student Dropout')
                ->performedOn($dropout)
                ->causedBy(Auth::user())
                ->withProperties([
                    'student_id' => $dropout->student_id,
                    'reason' => $dropout->reason,
                    'dropout_date' => $dropout->dropout_date,
                ])
                ->log('Student dropout record updated');

            DB::commit();

            return new StudentDropoutResource(
                $dropout->load('student', 'academicYear', 'schoolClass', 'section')
            );
            
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }

    public function destroy(int $id)
    {
        DB::beginTransaction();

        try {
            
            // Find the record
            $dropout = StudentDropout::findOrFail($id);

            if ($dropout) {

                $requestData = new Request([
                    'status' => 'Active'
                ]);
                
                $this->studentService->changeStatus($requestData, $dropout->student_id);
            }

            // Activity log before deleting
            activity('Student Dropout')
                ->performedOn($dropout)
                ->causedBy(Auth::user())
                ->withProperties([
                    'student_id' => $dropout->student_id,
                    'dropout_date' => $dropout->dropout_date,
                    'reason' => $dropout->reason,
                ])
                ->log('Student dropout record deleted');

            // Delete the record
            $dropout->delete();

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

            // Find the dropout record
            $dropout = StudentDropout::findOrFail($id);

            // Update reason & remarks
            $dropout->reason = $request->reason;
            if ($request->has('reason_details')) {
                $dropout->reason_details = $request->reason_details;
            }
            $dropout->updated_at = now();
            $dropout->save();

            // Activity log
            activity('Student Dropout Status')
                ->performedOn($dropout)
                ->causedBy(Auth::user())
                ->withProperties([
                    'student_id' => $dropout->student_id,
                    'reason' => $dropout->reason,
                    'remarks' => $dropout->remarks,
                ])
                ->log('Student dropout reason updated');

            DB::commit();

            return new StudentDropoutResource($dropout);

        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }
}