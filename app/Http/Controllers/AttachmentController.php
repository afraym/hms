<?php

namespace App\Http\Controllers;

use App\Models\Attachment;
use Illuminate\Http\Request;

class AttachmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // Debug: Log the incoming request
            \Log::info('AttachmentController store request', [
                'patient_id' => $request->patient_id,
                'has_files' => $request->hasFile('attachments'),
                'files_count' => $request->hasFile('attachments') ? count($request->file('attachments')) : 0,
                'request_data' => $request->all()
            ]);

            // Check if files exist first
            if (!$request->hasFile('attachments') || empty($request->file('attachments'))) {
                return response()->json([
                    'success' => false,
                    'message' => 'يرجى اختيار ملف واحد على الأقل'
                ], 422);
            }

            $messages = [
                'patient_id.required' => 'رقم المريض مطلوب',
                'patient_id.exists' => 'المريض غير موجود',
                'attachments.*.file' => 'الملف المرفوع غير صحيح',
                'attachments.*.max' => 'حجم الملف يجب ألا يتجاوز 10 ميجابايت',
                'attachments.*.mimes' => 'نوع الملف غير مسموح. الأنواع المسموحة: pdf, doc, docx, jpg, jpeg, png, gif',
            ];

            $validated = $request->validate([
                'patient_id' => 'required|exists:patients,id',
                'attachments.*' => 'required|file|max:10240|mimes:pdf,doc,docx,jpg,jpeg,png,gif',
                'description' => 'nullable|string|max:255',
            ], $messages);

            $patient = \App\Models\Patient::findOrFail($request->patient_id);
            $uploadedFiles = [];
            $errors = [];

            foreach ($request->file('attachments') as $index => $file) {
                try {
                    // Check if file is valid
                    if (!$file->isValid()) {
                        $errors[] = "الملف رقم " . ($index + 1) . " غير صالح";
                        continue;
                    }

                    // Generate unique filename
                    $originalName = $file->getClientOriginalName();
                    $extension = $file->getClientOriginalExtension();
                    $filename = pathinfo($originalName, PATHINFO_FILENAME);
                    $uniqueName = $filename . '_' . time() . '_' . uniqid() . '.' . $extension;
                    
                    // Store the file in patient-specific directory
                    $path = $file->storeAs('patient_attachments/' . $patient->id, $uniqueName, 'public');
                    
                    if (!$path) {
                        $errors[] = "فشل في حفظ الملف: " . $originalName;
                        continue;
                    }

                    // Create attachment record
                    $attachment = $patient->attachments()->create([
                        'file' => $path,
                        'original_name' => $originalName,
                        'type' => $file->getClientMimeType(),
                        'description' => $request->description ?? null,
                    ]);

                    $uploadedFiles[] = $attachment;

                    \Log::info('File uploaded successfully', [
                        'file' => $originalName,
                        'path' => $path,
                        'attachment_id' => $attachment->id
                    ]);

                } catch (\Exception $e) {
                    \Log::error('Error uploading individual file: ' . $e->getMessage());
                    $errors[] = "خطأ في رفع الملف: " . ($file->getClientOriginalName() ?? 'غير معروف');
                }
            }

            if (empty($uploadedFiles)) {
                $errorMessage = 'فشل في رفع جميع الملفات.';
                if (!empty($errors)) {
                    $errorMessage .= ' الأخطاء: ' . implode(', ', $errors);
                }
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage
                ], 500);
            }

            $message = count($uploadedFiles) === 1 
                ? 'تم رفع الملف بنجاح' 
                : 'تم رفع ' . count($uploadedFiles) . ' ملف بنجاح';

            if (!empty($errors)) {
                $message .= '. أخطاء: ' . implode(', ', $errors);
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'uploaded_count' => count($uploadedFiles)
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation error in AttachmentController store', [
                'errors' => $e->errors(),
                'message' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'خطأ في التحقق من صحة البيانات',
                'errors' => $e->errors()
            ], 422);
            
        } catch (\Exception $e) {
            \Log::error('Error in AttachmentController store: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء رفع الملفات: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Attachment $attachment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Attachment $attachment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Attachment $attachment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Attachment $attachment)
    {
        //
    }
}
