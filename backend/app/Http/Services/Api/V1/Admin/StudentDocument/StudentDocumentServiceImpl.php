<?php

namespace App\Http\Services\Api\V1\Admin\StudentDocument;

use App\Helper\ImageUpload;
use App\Http\Resources\Api\V1\Admin\StudentDocumentResource;
use App\Models\StudentDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class StudentDocumentServiceImpl implements StudentDocumentService
{
    public function index(Request $request)
    {
        $student_document = StudentDocument::with('student', 'uploader', 'verifier');

        // Sorting (secure)
        $sortableColumns = ['id', 'document_type', 'created_at'];

        $sortBy = $request->get('sortBy', 'id');
        $sortDesc = $request->get('sortDesc', 'true') === 'true' ? 'desc' : 'asc';

        if (! in_array($sortBy, $sortableColumns)) {
            $sortBy = 'id';
        }

        $student_document->orderBy($sortBy, $sortDesc);

        // Search
        if ($search = $request->get('search')) {
            $student_document->where('document_type', 'like', "%{$search}%");
        }

        // Pagination
        $itemsPerPage = (int) $request->get('itemsPerPage', 10);
        $student_documents = $student_document->paginate($itemsPerPage);

        return StudentDocumentResource::collection($student_documents);
    }

    public function getAllStudentDocuments()
    {
        $student_document = StudentDocument::with('student', 'uploader', 'verifier')->latest()->get();

        return StudentDocumentResource::collection($student_document);
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {

            $student_document = new StudentDocument();

            $student_document->student_id = $request->student_id;
            $student_document->document_type = $request->document_type;
            $student_document->document_name = $request->document_name;
            $student_document->uploaded_by = Auth::id();
            $student_document->uploaded_at = now();

            if ($request->hasFile('document_path')) {
                $documentPath = ImageUpload::uploadImageApplicationStorage(
                    $request->file('document_path'),
                    'students-documents'
                );

                $student_document->document_path = $documentPath;
                $student_document->document_url = asset('storage/' . $documentPath);
            }

            $student_document->save();

            // Activity Log
            activity('Student Document')
                ->performedOn($student_document)
                ->causedBy(Auth::id())
                ->withProperties([
                    'student_id'    => $student_document->student_id,
                    'document_type' => $student_document->document_type,
                    'document_name' => $student_document->document_name,
                ])
                ->log('Student document uploaded');

            DB::commit();

            return new StudentDocumentResource(
                $student_document->load('student', 'uploader', 'verifier')
            );

        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }

    public function show(int $id)
    {
        $student_document = StudentDocument::with('student', 'uploader', 'verifier')->findOrFail($id);

        return new StudentDocumentResource($student_document);
    }

    public function update(Request $request, int $id)
    {
        DB::beginTransaction();

        try {

            $student_document = StudentDocument::findOrFail($id);

            $student_document->student_id    = $request->student_id ?? $student_document->student_id;
            $student_document->document_type = $request->document_type ?? $student_document->document_type;
            $student_document->document_name = $request->document_name ?? $student_document->document_name;

            // Replace file if new one uploaded
            if ($request->hasFile('document_path')) {

                // (Optional) delete old file if exists
                if ($student_document->document_path && Storage::disk('public')->exists($student_document->document_path)) {
                    ImageUpload::deleteApplicationStorage($student_document->document_path);
                }

                $documentPath = ImageUpload::uploadImageApplicationStorage(
                    $request->file('document_path'),
                    'students-documents'
                );

                $student_document->document_path = $documentPath;
                $student_document->document_url  = asset('storage/' . $documentPath);
            }

            $student_document->save();

            // Activity Log
            activity('Student Document')
                ->performedOn($student_document)
                ->causedBy(Auth::id())
                ->withProperties([
                    'student_id'    => $student_document->student_id,
                    'document_type' => $student_document->document_type,
                    'document_name' => $student_document->document_name,
                ])
                ->log('Student document updated');

            DB::commit();

            return new StudentDocumentResource(
                $student_document->load('student', 'uploader', 'verifier')
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

            $student_document = StudentDocument::findOrFail($id);

            // Delete the file if it exists
            if ($student_document->document_path && Storage::disk('public')->exists($student_document->document_path)) {
                ImageUpload::deleteApplicationStorage($student_document->document_path);
            }

            $student_document->delete();

            // Activity Log
            activity('Student Document')
                ->performedOn($student_document)
                ->causedBy(Auth::id())
                ->withProperties([
                    'student_id'    => $student_document->student_id,
                    'document_type' => $student_document->document_type,
                    'document_name' => $student_document->document_name,
                ])
                ->log('Student document deleted');

            DB::commit();

        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }

    public function verifiy(Request $request, int $id)
    {
        DB::beginTransaction();

        try {

            $student_document = StudentDocument::findOrFail($id);

            $student_document->is_verified = $request->is_verified;
            $student_document->verified_by = Auth::id() ?? null;
            $student_document->verified_at = now();

            $student_document->save();

            // Activity Log
            activity('Student Document Verification')
                ->performedOn($student_document)
                ->causedBy(Auth::id())
                ->withProperties([
                    'student_id'    => $student_document->student_id,
                    'document_type' => $student_document->document_type,
                    'document_name' => $student_document->document_name,
                ])
                ->log('Student document verified');

            DB::commit();

            return new StudentDocumentResource(
                $student_document->load('student', 'uploader', 'verifier')
            );

        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }
}