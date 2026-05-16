<?php

namespace App\Http\Controllers;

use App\Models\AccreditationRequest;
use App\Models\FormSubmission;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Spatie\LaravelPdf\Facades\Pdf;
use ZipArchive;

class PrintController extends Controller
{
    /**
     * Print Stage Two data as PDF and bundle with attachments in a ZIP file.
     */
    public function printStageTwo(AccreditationRequest $accreditationRequest, FormSubmission $formSubmission)
    {
        // 1. Data Fetching
        $accreditationRequest->load([
            'program.department.college.university.officer',
            'programCoordinator',
        ]);

        $program = $accreditationRequest->program;
        $department = $program->department;
        $college = $department->college;
        $university = $college->university;

        $data = $formSubmission->form_data;

        // Prepare Decision Names Mapping
        $decisionNames = [
            1 => 'قرار إنشاء البرنامج',
            2 => 'قرار الطاقة الاستيعابية',
            3 => 'قرار قبول أول دفعة',
            4 => 'قرار قبول دفعة العام الماضي',
            5 => 'قرار قبول دفعة العام قبل الماضي',
            6 => 'قرار اعتماد أحدث خطة دراسية',
            7 => 'محضر قرار تخرج دفعة العام الحالي',
            8 => 'قرار تقديم طلب الاعتماد الأكاديمي',
        ];

        // 2. PDF Generation
        try {
            $pdfContent = Pdf::view('print_templates.Basic_data_template', [
                'accreditationRequest' => $accreditationRequest,
                'formSubmission' => $formSubmission,
                'program' => $program,
                'department' => $department,
                'college' => $college,
                'university' => $university,
                'data' => $data,
                'decisionNames' => $decisionNames,
                'isPrint' => true,
            ])
                ->format('a4')
                ->withBrowsershot(function ($browsershot) {
                    $browsershot->waitUntilNetworkIdle();
                })
                ->name($program->program_name.'.pdf')
                ->generatePdfContent();
        } catch (\Exception $e) {
            return back()->with('error', 'خطأ في إنشاء ملف PDF (Spatie): '.$e->getMessage());
        }

        // 3. ZIP Creation
        $zipName = 'Program_Data_'.Carbon::now()->format('YmdHis').'.zip';
        $tempDir = storage_path('app/temp_print_'.uniqid());
        File::makeDirectory($tempDir);

        // Save PDF to temp dir
        $pdfFileName = 'البيانات_الأساسية_'.str_replace(['/', '\\'], '_', $program->program_name).'.pdf';
        File::put($tempDir.'/'.$pdfFileName, $pdfContent);

        // Create Decisions folder
        $decisionsDir = $tempDir.'/ملفات القرارات';
        File::makeDirectory($decisionsDir);

        // Copy attachments
        $decisionFiles = $data['decision_files'] ?? [];
        foreach ($decisionFiles as $index => $path) {
            $fileContent = null;
            if (Storage::disk('local')->exists($path)) {
                $fileContent = Storage::disk('local')->get($path);
            } elseif (Storage::disk('public')->exists($path)) {
                $fileContent = Storage::disk('public')->get($path);
            }

            if ($fileContent) {
                $extension = pathinfo($path, PATHINFO_EXTENSION) ?: 'pdf';
                $decisionName = $decisionNames[$index] ?? 'قرار_'.$index;
                $newFileName = $decisionName.'.'.$extension;

                // Copy from storage to temp dir
                File::put($decisionsDir.'/'.$newFileName, $fileContent);
            }
        }

        // Zip everything
        $zipPath = storage_path('app/'.$zipName);
        $zip = new ZipArchive;
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
            $files = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($tempDir),
                \RecursiveIteratorIterator::LEAVES_ONLY
            );

            foreach ($files as $name => $file) {
                if (! $file->isDir()) {
                    $filePath = $file->getRealPath();
                    $relativePath = substr($filePath, strlen($tempDir) + 1);
                    $zip->addFile($filePath, $relativePath);
                }
            }
            $zip->close();
        }

        // Cleanup temp dir
        File::deleteDirectory($tempDir);

        // 4. Return as Streamed Response to show progress in browser
        return response()->streamDownload(function () use ($zipPath) {
            $stream = fopen($zipPath, 'rb');
            while (! feof($stream)) {
                echo fread($stream, 8192);
                flush();
            }
            fclose($stream);

            // Cleanup zip file after streaming
            if (file_exists($zipPath)) {
                unlink($zipPath);
            }
        }, $zipName, [
            'Content-Type' => 'application/zip',
            'Content-Length' => filesize($zipPath),
        ]);
    }
}
