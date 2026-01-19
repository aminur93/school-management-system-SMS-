<?php

namespace App\Http\Services\Api\V1\Admin\TransferCertificate;

use App\Helper\ImageUpload;
use App\Http\Resources\Api\V1\Admin\TransferCertificateResource;
use App\Models\TransferCertificate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class TransferCertificateServiceImpl implements TransferCertificateService
{
    public function index(Request $request)
    {
        $transfer_certificate = TransferCertificate::with('student', 'academicYear', 'schoolClass', 'section', 'createdBy', 'updatedBy');

         /*
        |--------------------------------------------------------------------------
        | Sorting (Secure)
        |--------------------------------------------------------------------------
        */
        $sortableColumns = [
            'id',
            'issue_date',
            'leaving_date',
            'persion_character',
            'status',
            'created_at',
        ];

        $sortBy   = $request->get('sortBy', 'id');
        $sortDesc = $request->get('sortDesc', 'true') === 'true' ? 'desc' : 'asc';

        if (! in_array($sortBy, $sortableColumns)) {
            $sortBy = 'id';
        }

        $transfer_certificate->orderBy($sortBy, $sortDesc);

        /*
        |--------------------------------------------------------------------------
        | Relation Wise Search
        |--------------------------------------------------------------------------
        */
        if ($search = $request->get('search')) {

            $transfer_certificate->where(function ($q) use ($search) {

                // Promotion Fields
                $q->where('issue_date', 'ILIKE', "%{$search}%")
                ->orWhere('leaving_date', 'ILIKE', "%{$search}%")
                ->orWhere('status', 'ILIKE', "%{$search}%");

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

        /*
        |--------------------------------------------------------------------------
        | Pagination
        |--------------------------------------------------------------------------
        */
        $itemsPerPage = (int) $request->get('itemsPerPage', 10);
        $transfer_certificates = $transfer_certificate->paginate($itemsPerPage);

        return TransferCertificateResource::collection($transfer_certificates);
    }

    public function getAllTransferCertificates()
    {
        $transfer_certificates = TransferCertificate::with('student', 'academicYear', 'schoolClass', 'section', 'createdBy', 'updatedBy')->latest()->get();

        return TransferCertificateResource::collection($transfer_certificates);
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            
            $tc = new TransferCertificate();

            // Student & Identity
            $tc->student_id = $request->student_id;
            $tc->tc_number = $request->tc_number;

            // Academic Info
            $tc->academic_year_id = $request->academic_year_id;
            $tc->school_class_id = $request->school_class_id;
            $tc->section_id = $request->section_id;

            // TC Details
            $tc->issue_date = $request->issue_date;
            $tc->leaving_date = $request->leaving_date;
            $tc->reason = $request->reason;
            $tc->reason_details = $request->reason_details;

            // Character & Conduct
            $tc->persion_character = $request->persion_character;
            $tc->conduct = $request->conduct;

            // Academic Performance
            $tc->last_exam_passed = $request->last_exam_passed;
            $tc->last_exam_result = $request->last_exam_result;
            $tc->total_working_days = $request->total_working_days;
            $tc->total_present_days = $request->total_present_days;
            $tc->attendance_percentage = $request->attendance_percentage;

            // New School Info
            $tc->new_school_name = $request->new_school_name;
            $tc->new_school_address = $request->new_school_address;

            // TC Document Upload (optional)
            if ($request->hasFile('tc_document')) {

                $path = ImageUpload::uploadImageApplicationStorage(
                    $request->file('tc_document'),
                    'transfer-certificates'
                );

                $tc->tc_document_path = $path;
                $tc->tc_document_path_url = asset('storage/' . $path);
            }

            // Status & Audit
            $tc->status = 'Requested';
            $tc->requested_by = Auth::id();

            $tc->save();

            // Activity Log
            activity('Transfer Certificate')
                ->performedOn($tc)
                ->causedBy(Auth::user())
                ->withProperties([
                    'tc_number' => $tc->tc_number,
                    'student_id' => $tc->student_id,
                    'status' => $tc->status,
                ])
                ->log('Transfer Certificate requested');

            DB::commit();

            return new TransferCertificateResource($tc);

        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }

    public function show(int $id)
    {
        $transfer_certificate = TransferCertificate::with('student', 'academicYear', 'schoolClass', 'section', 'createdBy', 'updatedBy')->findOrFail($id);

        return new TransferCertificateResource($transfer_certificate);
    }

    public function update(Request $request, int $id)
    {
        DB::beginTransaction();

        try {
            
            $tc = TransferCertificate::findOrFail($id);

            // Student & Academic Info
            $tc->student_id = $request->student_id ?? $tc->student_id;
            $tc->academic_year_id = $request->academic_year_id ?? $tc->academic_year_id;
            $tc->school_class_id = $request->school_class_id ?? $tc->school_class_id;
            $tc->section_id = $request->section_id ?? $tc->section_id;

            // TC Details
            $tc->issue_date = $request->issue_date ?? $tc->issue_date;
            $tc->leaving_date = $request->leaving_date ?? $tc->leaving_date;
            $tc->reason = $request->reason ?? $tc->reason;
            $tc->reason_details = $request->reason_details ?? $tc->reason_details;

            // Character & Conduct
            $tc->persion_character = $request->persion_character ?? $tc->persion_character;
            $tc->conduct = $request->conduct ?? $tc->conduct;

            // Academic Performance
            $tc->last_exam_passed = $request->last_exam_passed ?? $tc->last_exam_passed;
            $tc->last_exam_result = $request->last_exam_result ?? $tc->last_exam_result;
            $tc->total_working_days = $request->total_working_days ?? $tc->total_working_days;
            $tc->total_present_days = $request->total_present_days ?? $tc->total_present_days;
            $tc->attendance_percentage = $request->attendance_percentage ?? $tc->attendance_percentage;

            // New School Info
            $tc->new_school_name = $request->new_school_name ?? $tc->new_school_name;
            $tc->new_school_address = $request->new_school_address ?? $tc->new_school_address;

            // Replace TC Document (optional)
            if ($request->hasFile('tc_document')) {

                // Optional: delete old file if exists
                if ($tc->tc_document_path && Storage::disk('public')->exists($tc->tc_document_path)) {

                    ImageUpload::deleteApplicationStorage($tc->tc_document_path);
                }

                $path = ImageUpload::uploadImageApplicationStorage(
                    $request->file('tc_document'),
                    'transfer-certificates'
                );

                $tc->tc_document_path = $path;
                $tc->tc_document_path_url = asset('storage/' . $path);
            }

            $tc->save();

            // Activity Log
            activity('Transfer Certificate')
                ->performedOn($tc)
                ->causedBy(Auth::user())
                ->withProperties([
                    'tc_number' => $tc->tc_number,
                    'status' => $tc->status,
                    'updated_fields' => array_keys($request->all())
                ])
                ->log('Transfer Certificate updated');

            DB::commit();

            return new TransferCertificateResource($tc);

        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }

    public function destroy(int $id)
    {
        DB::beginTransaction();

        try {

            $tc = TransferCertificate::findOrFail($id);

            // Delete TC document file if exists
            if ($tc->tc_document_path && Storage::disk('public')->exists($tc->tc_document_path)) {

                ImageUpload::deleteApplicationStorage($tc->tc_document_path);
            }

            // Activity Log before delete
            activity('Transfer Certificate')
                ->performedOn($tc)
                ->causedBy(Auth::user())
                ->withProperties([
                    'tc_number' => $tc->tc_number,
                    'student_id' => $tc->student_id,
                    'status' => $tc->status,
                ])
                ->log('Transfer Certificate deleted');

            // Delete record
            $tc->delete();

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

            $tc = TransferCertificate::findOrFail($id);
            $oldStatus = $tc->status;
            $newStatus = $request->status;

            // Status based updates
            if ($newStatus === 'Approved') {
                $tc->approved_by = Auth::id();
                $tc->approved_at = now();
            }

            if ($newStatus === 'Issued') {
                $tc->issued_by = Auth::id();
            }

            if ($newStatus === 'Cancelled') {
                // Optional: reset approvals
                $tc->approved_by = null;
                $tc->approved_at = null;
                $tc->issued_by = null;
            }

            $tc->status = $newStatus;
            $tc->remarks = $request->remarks ?? $tc->remarks;
            $tc->persion_character = $request->persion_character ?? $tc->persion_character;
            $tc->conduct = $request->conduct ?? $tc->conduct;

            $tc->save();

            // Activity Log
            activity('Transfer Certificate')
                ->performedOn($tc)
                ->causedBy(Auth::user())
                ->withProperties([
                    'tc_number' => $tc->tc_number,
                    'old_status' => $oldStatus,
                    'new_status' => $newStatus,
                ])
                ->log('Transfer Certificate status updated');

            DB::commit();

            return new TransferCertificateResource($tc);

        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
}