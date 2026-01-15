<?php

namespace App\Http\Services\Api\V1\Admin\StudentPromotion;

use App\Http\Resources\Api\V1\Admin\StudentPromotionResource;
use App\Models\StudentPromotion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StudentPromotionServiceImpl implements StudentPromotionService
{
    public function index(Request $request)
    {
        $query = StudentPromotion::with([
            'student',
            'fromAcademicYear',
            'fromClass',
            'fromSection',
            'toAcademicYear',
            'toClass',
            'toSection',
            'createdBy',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Sorting (Secure)
        |--------------------------------------------------------------------------
        */
        $sortableColumns = [
            'id',
            'promotion_date',
            'promotion_type',
            'result_status',
            'created_at',
        ];

        $sortBy   = $request->get('sortBy', 'id');
        $sortDesc = $request->get('sortDesc', 'true') === 'true' ? 'desc' : 'asc';

        if (! in_array($sortBy, $sortableColumns)) {
            $sortBy = 'id';
        }

        $query->orderBy($sortBy, $sortDesc);

        /*
        |--------------------------------------------------------------------------
        | Relation Wise Search
        |--------------------------------------------------------------------------
        */
        if ($search = $request->get('search')) {

            $query->where(function ($q) use ($search) {

                // Promotion Fields
                $q->where('promotion_type', 'ILIKE', "%{$search}%")
                ->orWhere('result_status', 'ILIKE', "%{$search}%")
                ->orWhere('remarks', 'ILIKE', "%{$search}%");

                // Student
                $q->orWhereHas('student', function ($q) use ($search) {
                    $q->where('name', 'ILIKE', "%{$search}%")
                    ->orWhere('roll_number', 'ILIKE', "%{$search}%")
                    ->orWhere('registration_number', 'ILIKE', "%{$search}%");
                });

                // From Academic Year
                $q->orWhereHas('fromAcademicYear', function ($q) use ($search) {
                    $q->where('name', 'ILIKE', "%{$search}%")
                    ->orWhere('year', 'ILIKE', "%{$search}%");
                });

                // From Class
                $q->orWhereHas('fromClass', function ($q) use ($search) {
                    $q->where('name', 'ILIKE', "%{$search}%");
                });

                // From Section
                $q->orWhereHas('fromSection', function ($q) use ($search) {
                    $q->where('name', 'ILIKE', "%{$search}%");
                });

                // To Academic Year
                $q->orWhereHas('toAcademicYear', function ($q) use ($search) {
                    $q->where('name', 'ILIKE', "%{$search}%")
                    ->orWhere('year', 'ILIKE', "%{$search}%");
                });

                // To Class
                $q->orWhereHas('toClass', function ($q) use ($search) {
                    $q->where('name', 'ILIKE', "%{$search}%");
                });

                // To Section
                $q->orWhereHas('toSection', function ($q) use ($search) {
                    $q->where('name', 'ILIKE', "%{$search}%");
                });
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Pagination
        |--------------------------------------------------------------------------
        */
        $itemsPerPage = (int) $request->get('itemsPerPage', 10);
        $studentPromotions = $query->paginate($itemsPerPage);

        return StudentPromotionResource::collection($studentPromotions);
    }

    public function getAllStudentPromotions()
    {
        $studentPromotions = StudentPromotion::with([
            'student',
            'fromAcademicYear',
            'fromClass',
            'fromSection',
            'toAcademicYear',
            'toClass',
            'toSection',
            'createdBy',
        ])
        ->latest()
        ->get();

        return StudentPromotionResource::collection($studentPromotions);
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            
            // Create Student Promotion
            $promotion = new StudentPromotion();

            // Student
            $promotion->student_id = $request->student_id;

            // From Details
            $promotion->from_academic_year_id = $request->from_academic_year_id;
            $promotion->from_class_id         = $request->from_class_id;
            $promotion->from_section_id       = $request->from_section_id;

            // To Details
            $promotion->to_academic_year_id   = $request->to_academic_year_id;
            $promotion->to_class_id           = $request->to_class_id;
            $promotion->to_section_id         = $request->to_section_id;

            // Promotion Details
            $promotion->promotion_date = $request->promotion_date;
            $promotion->promotion_type = $request->promotion_type ?? 'Promoted';
            $promotion->result_status  = $request->result_status;

            // Academic Performance
            $promotion->total_marks    = $request->total_marks;
            $promotion->obtained_marks = $request->obtained_marks;
            $promotion->percentage     = $request->percentage;
            $promotion->grade          = $request->grade;
            $promotion->gpa            = $request->gpa;

            // Other Info
            $promotion->remarks      = $request->remarks;
            $promotion->is_processed = $request->is_processed ?? false;

            // Audit
            $promotion->created_by = Auth::id() ?? null;

            $promotion->save();

            /*
            |--------------------------------------------------------------------------
            | Activity Log
            |--------------------------------------------------------------------------
            */
            activity('Student Promotion')
                ->performedOn($promotion)
                ->causedBy(Auth::user())
                ->withProperties([
                    'attributes' => $promotion->toArray()
                ])
                ->log('Student promotion created successfully');

            DB::commit();

            return new StudentPromotionResource(
                $promotion->load(
                    'student',
                    'fromAcademicYear',
                    'fromClass',
                    'fromSection',
                    'toAcademicYear',
                    'toClass',
                    'toSection',
                    'createdBy'
                )
            );

        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }

    public function show(int $id)
    {
         $studentPromotion = StudentPromotion::with([
            'student',
            'fromAcademicYear',
            'fromClass',
            'fromSection',
            'toAcademicYear',
            'toClass',
            'toSection',
            'createdBy',
        ])
        ->findOrFail($id);

        return new StudentPromotionResource($studentPromotion);
    }

    public function update(Request $request, int $id)
    {
        DB::beginTransaction();

        try {
            
             $promotion = StudentPromotion::findOrFail($id);

            /*
            |--------------------------------------------------------------------------
            | Update Student Promotion
            |--------------------------------------------------------------------------
            */

            // Student (normally immutable, but keeping flexible)
            $promotion->student_id = $request->student_id ?? $promotion->student_id;

            // From Details
            $promotion->from_academic_year_id = $request->from_academic_year_id ?? $promotion->from_academic_year_id;
            $promotion->from_class_id         = $request->from_class_id ?? $promotion->from_class_id;
            $promotion->from_section_id       = $request->from_section_id ?? $promotion->from_section_id;

            // To Details
            $promotion->to_academic_year_id   = $request->to_academic_year_id ?? $promotion->to_academic_year_id;
            $promotion->to_class_id           = $request->to_class_id ?? $promotion->to_class_id;
            $promotion->to_section_id         = $request->to_section_id ?? $promotion->to_section_id;

            // Promotion Details
            $promotion->promotion_date = $request->promotion_date ?? $promotion->promotion_date;
            $promotion->promotion_type = $request->promotion_type ?? $promotion->promotion_type;
            $promotion->result_status  = $request->result_status ?? $promotion->result_status;

            // Academic Performance
            $promotion->total_marks    = $request->total_marks ?? $promotion->total_marks;
            $promotion->obtained_marks = $request->obtained_marks ?? $promotion->obtained_marks;
            $promotion->percentage     = $request->percentage ?? $promotion->percentage;
            $promotion->grade          = $request->grade ?? $promotion->grade;
            $promotion->gpa            = $request->gpa ?? $promotion->gpa;

            // Other Info
            $promotion->remarks      = $request->remarks ?? $promotion->remarks;
            $promotion->is_processed = $request->has('is_processed')
                ? $request->is_processed
                : $promotion->is_processed;

            $promotion->save();

            /*
            |--------------------------------------------------------------------------
            | Activity Log
            |--------------------------------------------------------------------------
            */
            activity('Student Promotion')
                ->performedOn($promotion)
                ->causedBy(Auth::user())
                ->withProperties([
                    'updated_attributes' => $request->all()
                ])
                ->log('Student promotion updated successfully');

            DB::commit();

            return new StudentPromotionResource(
                $promotion->load(
                    'student',
                    'fromAcademicYear',
                    'fromClass',
                    'fromSection',
                    'toAcademicYear',
                    'toClass',
                    'toSection',
                    'createdBy'
                )
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
            
            $promotion = StudentPromotion::findOrFail($id);

            /*
            |--------------------------------------------------------------------------
            | Business Rule: Prevent delete if already processed
            |--------------------------------------------------------------------------
            */
            if ($promotion->is_processed) {
                abort(403, 'Processed promotion cannot be deleted.');
            }

            /*
            |--------------------------------------------------------------------------
            | Delete Promotion
            |--------------------------------------------------------------------------
            */
            $promotion->delete();

            /*
            |--------------------------------------------------------------------------
            | Activity Log
            |--------------------------------------------------------------------------
            */
            activity('Student Promotion')
                ->performedOn($promotion)
                ->causedBy(Auth::user())
                ->withProperties([
                    'deleted_id' => $id,
                    'student_id' => $promotion->student_id
                ])
                ->log('Student promotion deleted successfully');

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

            /*
            |--------------------------------------------------------------------------
            | Validate Input
            |--------------------------------------------------------------------------
            */
            $request->validate([
                'result_status' => 'required|in:Pass,Fail,Conditional',
            ]);

            /*
            |--------------------------------------------------------------------------
            | Find Promotion
            |--------------------------------------------------------------------------
            */
            $promotion = StudentPromotion::findOrFail($id);

            /*
            |--------------------------------------------------------------------------
            | Update Result Status
            |--------------------------------------------------------------------------
            */
            $promotion->result_status = $request->result_status;

            /*
            |--------------------------------------------------------------------------
            | Business Logic based on Result Status
            |--------------------------------------------------------------------------
            */
            if ($request->result_status === 'Pass') {
                $promotion->promotion_type = 'Promoted';
                $promotion->is_processed = true;
            }

            if ($request->result_status === 'Fail') {
                $promotion->promotion_type = 'Detained';
                $promotion->is_processed = true;
            }

            if ($request->result_status === 'Conditional') {
                $promotion->promotion_type = 'Promoted';
                $promotion->is_processed = false;
            }

            $promotion->save();

            /*
            |--------------------------------------------------------------------------
            | Activity Log
            |--------------------------------------------------------------------------
            */
            activity('Student Promotion')
                ->performedOn($promotion)
                ->causedBy(Auth::user())
                ->withProperties([
                    'promotion_id' => $promotion->id,
                    'result_status' => $promotion->result_status,
                    'promotion_type' => $promotion->promotion_type
                ])
                ->log('Student promotion result status updated');

            DB::commit();

            return new StudentPromotionResource($promotion);

        } catch (\Throwable $th) {
            DB::rollBack();
            
            throw $th;
        }
    }

}