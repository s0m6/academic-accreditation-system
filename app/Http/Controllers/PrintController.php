<?php

namespace App\Http\Controllers;

use App\Models\AccreditationRequest;
use App\Models\FormSubmission;
use App\Models\Standard;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use setasign\Fpdi\Fpdi;
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
                ->name($program->program_name . '.pdf')
                ->generatePdfContent();
        } catch (\Exception $e) {
            return back()->with('error', 'خطأ في إنشاء ملف PDF (Spatie): ' . $e->getMessage());
        }

        // 3. ZIP Creation
        $zipName = 'Program_Data_' . Carbon::now()->format('YmdHis') . '.zip';
        $tempDir = storage_path('app/temp_print_' . uniqid());
        File::makeDirectory($tempDir);

        // Save PDF to temp dir
        $pdfFileName = 'البيانات_الأساسية_' . str_replace(['/', '\\'], '_', $program->program_name) . '.pdf';
        File::put($tempDir . '/' . $pdfFileName, $pdfContent);

        // Create Decisions folder
        $decisionsDir = $tempDir . '/ملفات القرارات';
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
                $decisionName = $decisionNames[$index] ?? 'قرار_' . $index;
                $newFileName = $decisionName . '.' . $extension;

                File::put($decisionsDir . '/' . $newFileName, $fileContent);
            }
        }

        // Zip everything
        $zipPath = storage_path('app/' . $zipName);
        $zip = new ZipArchive;
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
            $files = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($tempDir),
                \RecursiveIteratorIterator::LEAVES_ONLY
            );

            foreach ($files as $name => $file) {
                if (!$file->isDir()) {
                    $filePath = $file->getRealPath();
                    $relativePath = substr($filePath, strlen($tempDir) + 1);
                    $zip->addFile($filePath, $relativePath);
                }
            }
            $zip->close();
        }

        File::deleteDirectory($tempDir);

        return response()->streamDownload(function () use ($zipPath) {
            $stream = fopen($zipPath, 'rb');
            while (!feof($stream)) {
                echo fread($stream, 8192);
                flush();
            }
            fclose($stream);

            if (file_exists($zipPath)) {
                unlink($zipPath);
            }
        }, $zipName, [
            'Content-Type' => 'application/zip',
            'Content-Length' => filesize($zipPath),
        ]);
    }

    /**
     * Print Stage Three: self-study PDF + 7 standard PDFs merged with evidences, all in one ZIP.
     */
    public function printStageThree(AccreditationRequest $accreditationRequest, FormSubmission $formSubmission)
    {
        set_time_limit(300);

        // 1. Load Relations
        $accreditationRequest->load([
            'program.department.college.university.officer',
            'programCoordinator',
        ]);

        $program = $accreditationRequest->program;
        $department = $program->department;
        $college = $department->college;
        $university = $college->university;
        $formData = $formSubmission->form_data ?? [];

        // 2. Load Standards with all sub-standards and indicators
        $standards = Standard::with(['subStandards.indicators'])
            ->orderBy('id')
            ->get();

        // 3. Load all indicator evaluations with evidences for this submission
        $indicatorEvaluations = $formSubmission->indicatorEvaluations()
            ->with('evidences')
            ->get()
            ->keyBy('indicator_id');

        // Map evidences by indicator_id for easy lookup in templates
        $evidencesByIndicatorId = $indicatorEvaluations->mapWithKeys(function ($ie) {
            return [$ie->indicator_id => $ie->evidences];
        });

        $indicatorScores = $indicatorEvaluations->map(fn($ie) => $ie->score);

        // 4. Create temp directory
        $tempDir = storage_path('app/temp_s3_' . uniqid());
        File::makeDirectory($tempDir, 0755, true);
        $standardsDir = $tempDir . '/المعايير';
        File::makeDirectory($standardsDir, 0755, true);

        try {
            // 5. Generate Self-Study PDF (Part 1 + Part 3)
            $selfStudyPdf = Pdf::view('print_templates.self_study_template', [
                'accreditationRequest' => $accreditationRequest,
                'formSubmission' => $formSubmission,
                'program' => $program,
                'department' => $department,
                'college' => $college,
                'university' => $university,
                'formData' => $formData,
                'standards' => $standards,
                'indicatorScores' => $indicatorScores,
                'isPrint' => true,
            ])
                ->format('a4')
                ->withBrowsershot(fn($b) => $b->waitUntilNetworkIdle())
                ->generatePdfContent();

            $selfStudyFileName = 'الدراسة_الذاتية_' . str_replace([' ', '/', '\\'], '_', $program->program_name) . '.pdf';
            File::put($tempDir . '/' . $selfStudyFileName, $selfStudyPdf);

            // 6. Generate one PDF per standard, merged with evidence files
            foreach ($standards as $std) {
                $standardPdfPath = $this->buildStandardPdf(
                    $std,
                    $formData,
                    $indicatorEvaluations,
                    $evidencesByIndicatorId,
                    $university,
                    $college,
                    $tempDir
                );

                $safeName = $this->safeName('المعيار_' . $std->number . '_' . $std->name);
                File::move($standardPdfPath, $standardsDir . '/' . $safeName . '.pdf');
            }

        } catch (\Exception $e) {
            File::deleteDirectory($tempDir);

            return back()->with('error', 'خطأ أثناء إنشاء التقارير: ' . $e->getMessage());
        }

        // 7. Bundle everything in a ZIP
        $zipName = 'Self-Study-' . $accreditationRequest->id . '.zip';
        $zipPath = storage_path('app/' . $zipName);

        $zip = new ZipArchive;
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
            $files = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($tempDir),
                \RecursiveIteratorIterator::LEAVES_ONLY
            );
            foreach ($files as $file) {
                if (!$file->isDir()) {
                    $filePath = $file->getRealPath();
                    $relativePath = substr($filePath, strlen($tempDir) + 1);
                    $zip->addFile($filePath, $relativePath);
                }
            }
            $zip->close();
        }

        File::deleteDirectory($tempDir);

        return response()->streamDownload(function () use ($zipPath) {
            $stream = fopen($zipPath, 'rb');
            while (!feof($stream)) {
                echo fread($stream, 8192);
                flush();
            }
            fclose($stream);
            if (file_exists($zipPath)) {
                unlink($zipPath);
            }
        }, $zipName, [
            'Content-Type' => 'application/zip',
            'Content-Length' => filesize($zipPath),
        ]);
    }

    /**
     * Build a single standard PDF: standard report + evidence cover pages + evidence PDFs merged.
     *
     * @return string Path to the merged PDF file in temp dir
     */
    private function buildStandardPdf(
        Standard $std,
        array $formData,
        $indicatorEvaluations,
        $evidencesByIndicatorId,
        $university,
        $college,
        string $tempDir
    ): string {
        // Generate the standard report PDF
        $standardReportContent = Pdf::view('print_templates.standard_template', [
            'standard' => $std,
            'formData' => $formData,
            'indicatorEvaluations' => $indicatorEvaluations,
            'evidencesByIndicatorId' => $evidencesByIndicatorId,
            'university' => $university,
            'college' => $college,
        ])
            ->format('a4')
            ->withBrowsershot(fn($b) => $b->waitUntilNetworkIdle())
            ->generatePdfContent();

        $stdReportPath = $tempDir . '/std_report_' . $std->id . '_' . uniqid() . '.pdf';
        File::put($stdReportPath, $standardReportContent);

        // Collect all evidence PDF files for this standard in order
        $evidencePdfs = [];

        foreach ($std->subStandards as $sub) {
            foreach ($sub->indicators as $ind) {
                $evidences = $evidencesByIndicatorId[$ind->id] ?? collect();
                foreach ($evidences as $ev) {
                    $path = $ev->file_path;
                    $absolutePath = null;

                    if (Storage::disk('local')->exists($path)) {
                        $absolutePath = Storage::disk('local')->path($path);
                    }

                    if ($absolutePath && file_exists($absolutePath)) {
                        $extension = strtolower(pathinfo($absolutePath, PATHINFO_EXTENSION));
                        if ($extension === 'pdf') {
                            $evidencePdfs[] = [
                                'name' => $ev->file_name,
                                'indicatorNumber' => $ind->number,
                                'filePath' => $absolutePath,
                                'standardNumber' => $std->number,
                                'standardName' => $std->name,
                            ];
                        }
                    }
                }
            }
        }

        // If no evidence PDFs, return just the standard report
        if (empty($evidencePdfs)) {
            return $stdReportPath;
        }

        // Build merge list: [standard report, cover1, evidence1, cover2, evidence2, ...]
        $filesToMerge = [$stdReportPath];

        foreach ($evidencePdfs as $ev) {
            $coverContent = Pdf::view('print_templates.evidence_cover_template', [
                'evidenceName' => $ev['name'],
                'indicatorNumber' => $ev['indicatorNumber'],
                'standardNumber' => $ev['standardNumber'],
                'standardName' => $ev['standardName'],
            ])
                ->format('a4')
                ->withBrowsershot(fn($b) => $b->waitUntilNetworkIdle())
                ->generatePdfContent();

            $coverPath = $tempDir . '/cover_' . uniqid() . '.pdf';
            File::put($coverPath, $coverContent);

            $filesToMerge[] = $coverPath;
            $filesToMerge[] = $ev['filePath'];
        }

        // Merge into a single PDF using FPDI
        $mergedPath = $tempDir . '/merged_std_' . $std->id . '_' . uniqid() . '.pdf';
        $this->mergePdfs($filesToMerge, $mergedPath);

        // Cleanup temp cover pages and the intermediate standard report
        foreach ($filesToMerge as $f) {
            if (!str_contains($f, 'merged_std') && file_exists($f) && str_starts_with($f, $tempDir)) {
                @unlink($f);
            }
        }

        return $mergedPath;
    }

    /**
     * Merge multiple PDF files into one using FPDI.
     *
     * @param  string[]  $pdfPaths
     */
    private function mergePdfs(array $pdfPaths, string $outputPath): void
    {
        $fpdi = new Fpdi;
        $fpdi->SetAutoPageBreak(false);

        foreach ($pdfPaths as $pdfPath) {
            if (!file_exists($pdfPath)) {
                continue;
            }

            try {
                $pageCount = $fpdi->setSourceFile($pdfPath);
                for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                    $templateId = $fpdi->importPage($pageNo);
                    $size = $fpdi->getTemplateSize($templateId);

                    $fpdi->AddPage($size['width'] > $size['height'] ? 'L' : 'P', [$size['width'], $size['height']]);
                    $fpdi->useTemplate($templateId);
                }
            } catch (\Exception $e) {
                // Skip unreadable/encrypted PDFs gracefully
                continue;
            }
        }

        $fpdi->Output($outputPath, 'F');
    }

    /**
     * Sanitize a string for use as a filename.
     */
    private function safeName(string $name): string
    {
        return str_replace(['/', '\\', ':', '*', '?', '"', '<', '>', '|'], '_', $name);
    }
}
