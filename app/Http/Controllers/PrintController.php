<?php

namespace App\Http\Controllers;

use App\Models\AccreditationRequest;
use App\Models\CommitteeApproval;
use App\Models\CommitteeReport;
use App\Models\FormSubmission;
use App\Models\ReportScore;
use App\Models\ReportSignature;
use App\Models\Standard;
use App\Models\VisitSchedule;
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

        File::deleteDirectory($tempDir);

        return response()->streamDownload(function () use ($zipPath) {
            $stream = fopen($zipPath, 'rb');
            while (! feof($stream)) {
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

        $indicatorScores = $indicatorEvaluations->map(fn ($ie) => $ie->score);

        // 4. Create temp directory
        $tempDir = storage_path('app/temp_s3_'.uniqid());
        File::makeDirectory($tempDir, 0755, true);
        $standardsDir = $tempDir.'/المعايير';
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
                ->withBrowsershot(fn ($b) => $b->waitUntilNetworkIdle())
                ->generatePdfContent();

            $selfStudyFileName = 'الدراسة_الذاتية_'.str_replace([' ', '/', '\\'], '_', $program->program_name).'.pdf';
            File::put($tempDir.'/'.$selfStudyFileName, $selfStudyPdf);

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

                $safeName = $this->safeName('المعيار_'.$std->number.'_'.$std->name);
                File::move($standardPdfPath, $standardsDir.'/'.$safeName.'.pdf');
            }

        } catch (\Exception $e) {
            File::deleteDirectory($tempDir);

            return back()->with('error', 'خطأ أثناء إنشاء التقارير: '.$e->getMessage());
        }

        // 7. Bundle everything in a ZIP
        $zipName = 'Self-Study-'.$accreditationRequest->id.'.zip';
        $zipPath = storage_path('app/'.$zipName);

        $zip = new ZipArchive;
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
            $files = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($tempDir),
                \RecursiveIteratorIterator::LEAVES_ONLY
            );
            foreach ($files as $file) {
                if (! $file->isDir()) {
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
            while (! feof($stream)) {
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
            ->withBrowsershot(fn ($b) => $b->waitUntilNetworkIdle())
            ->generatePdfContent();

        $stdReportPath = $tempDir.'/std_report_'.$std->id.'_'.uniqid().'.pdf';
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
                ->withBrowsershot(fn ($b) => $b->waitUntilNetworkIdle())
                ->generatePdfContent();

            $coverPath = $tempDir.'/cover_'.uniqid().'.pdf';
            File::put($coverPath, $coverContent);

            $filesToMerge[] = $coverPath;
            $filesToMerge[] = $ev['filePath'];
        }

        // Merge into a single PDF using FPDI
        $mergedPath = $tempDir.'/merged_std_'.$std->id.'_'.uniqid().'.pdf';
        $this->mergePdfs($filesToMerge, $mergedPath);

        // Cleanup temp cover pages and the intermediate standard report
        foreach ($filesToMerge as $f) {
            if (! str_contains($f, 'merged_std') && file_exists($f) && str_starts_with($f, $tempDir)) {
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
            if (! file_exists($pdfPath)) {
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
     * Print Stage Five Visit Schedule as PDF.
     */
    public function printVisitSchedule(AccreditationRequest $accreditationRequest, VisitSchedule $visitSchedule)
    {
        $accreditationRequest->load([
            'program.department.college.university',
            'committee.chairEvaluator.user',
        ]);

        $program = $accreditationRequest->program;
        $university = $program->department->college->university;
        $scheduleData = $visitSchedule->schedule_data;

        return Pdf::view('print_templates.visit_schedule_template', [
            'accreditationRequest' => $accreditationRequest,
            'visitSchedule' => $visitSchedule,
            'program' => $program,
            'university' => $university,
            'scheduleData' => $scheduleData,
            'isPrint' => true,
        ])
            ->format('a4')
            ->name('Visit_Schedule_req_'.$accreditationRequest->id.'.pdf')
            ->download();
    }

    /**
     * Print Stage Six Field Visit Report as PDF.
     */
    public function printVisitReport(AccreditationRequest $accreditationRequest)
    {
        $report = $accreditationRequest->committeeReport;
        if (! $report) {
            abort(404, 'التقرير غير موجود.');
        }

        $accreditationRequest->load([
            'program.department.college.university.officer',
            'programCoordinator',
            'committee.chairEvaluator.user',
        ]);

        $program = $accreditationRequest->program;
        $department = $program->department;
        $college = $department->college;
        $university = $college->university;

        $committee = $accreditationRequest->committee;

        // Build members data for signatures (same logic as Final Report but form_type is 'form_5')
        $membersData = [];

        // 1. Get Chair
        $chairEvaluator = $committee?->chairEvaluator;
        if ($chairEvaluator) {
            $chairSig = ReportSignature::where('report_id', $report->id)
                ->whereNull('approval_id')
                ->where('form_type', 'form_5')
                ->latest()
                ->first();

            $membersData[] = [
                'name' => $chairEvaluator->user->name,
                'signature_path' => $chairSig?->signature_path,
                'is_chair' => true,
            ];
        }

        // 2. Get Members
        $members = $committee ? $committee->activeMembers->filter(fn ($m) => $m->evaluator_id !== $committee->chair_evaluator_id) : collect();

        foreach ($members as $member) {
            $latestApproval = CommitteeApproval::where('report_id', $report->id)
                ->where('member_id', $member->evaluator_id)
                ->where('review_round', 'stage6')
                ->where('status', 'approved')
                ->latest()
                ->first();

            $sigPath = null;
            if ($latestApproval) {
                $sig = ReportSignature::where('approval_id', $latestApproval->id)
                    ->where('form_type', 'form_5')
                    ->latest()
                    ->first();
                $sigPath = $sig?->signature_path;
            }

            $membersData[] = [
                'name' => $member->evaluator->user->name,
                'signature_path' => $sigPath,
                'is_chair' => false,
            ];
        }

        $form5Data = $report->form5_data ?? [];

        return Pdf::view('print_templates.visit_report_template', [
            'accreditationRequest' => $accreditationRequest,
            'report' => $report,
            'program' => $program,
            'department' => $department,
            'college' => $college,
            'university' => $university,
            'membersData' => $membersData,
            'form5Data' => $form5Data,
            'isPrint' => true,
        ])
            ->format('a4')
            ->name('Visit_Report_req_'.$accreditationRequest->id.'.pdf')
            ->download();
    }

    /**
     * Print Stage Six Program Assessment Metrics (Form 6 Rubrics) as PDF.
     */
    public function printRubrics(AccreditationRequest $accreditationRequest)
    {
        $report = $accreditationRequest->committeeReport;
        if (! $report) {
            abort(404, 'التقرير غير موجود.');
        }

        $accreditationRequest->load([
            'program.department.college.university.officer',
            'programCoordinator',
            'committee.chairEvaluator.user',
        ]);

        $program = $accreditationRequest->program;
        $department = $program->department;
        $college = $department->college;
        $university = $college->university;

        $committee = $accreditationRequest->committee;

        // Load all standards with sub-standards and indicators
        $standards = Standard::with(['subStandards.indicators'])
            ->orderBy('id')
            ->get();

        // Load all initial scores keyed by indicator_id.
        $savedScores = ReportScore::where('report_id', $report->id)
            ->where('score_type', 'Initial')
            ->pluck('score', 'indicator_id');

        $savedFormData = $report->form6_initial_data ?? [];

        // Build members data for signatures (same logic as Final Report but form_type is 'form_6_initial')
        $membersData = [];

        // 1. Get Chair
        $chairEvaluator = $committee?->chairEvaluator;
        if ($chairEvaluator) {
            $chairSig = ReportSignature::where('report_id', $report->id)
                ->whereNull('approval_id')
                ->where('form_type', 'form_6_initial')
                ->latest()
                ->first();

            $membersData[] = [
                'name' => $chairEvaluator->user->name,
                'signature_path' => $chairSig?->signature_path,
                'is_chair' => true,
            ];
        }

        // 2. Get Members
        $members = $committee ? $committee->activeMembers->filter(fn ($m) => $m->evaluator_id !== $committee->chair_evaluator_id) : collect();

        foreach ($members as $member) {
            $latestApproval = CommitteeApproval::where('report_id', $report->id)
                ->where('member_id', $member->evaluator_id)
                ->where('review_round', 'stage6')
                ->where('status', 'approved')
                ->latest()
                ->first();

            $sigPath = null;
            if ($latestApproval) {
                $sig = ReportSignature::where('approval_id', $latestApproval->id)
                    ->where('form_type', 'form_6_initial')
                    ->latest()
                    ->first();
                $sigPath = $sig?->signature_path;
            }

            $membersData[] = [
                'name' => $member->evaluator->user->name,
                'signature_path' => $sigPath,
                'is_chair' => false,
            ];
        }

        return Pdf::view('print_templates.rubrics_report_template', [
            'accreditationRequest' => $accreditationRequest,
            'report' => $report,
            'program' => $program,
            'department' => $department,
            'college' => $college,
            'university' => $university,
            'standards' => $standards,
            'savedScores' => $savedScores,
            'savedFormData' => $savedFormData,
            'membersData' => $membersData,
            'isPrint' => true,
        ])
            ->format('a4')
            ->name('Program_Assessment_Metrics_req_'.$accreditationRequest->id.'.pdf')
            ->download();
    }

    /**
     * Print Stage Eight Program Assessment Metrics (Final Rubrics) as PDF.
     */
    public function printFinalRubrics(AccreditationRequest $accreditationRequest)
    {
        $report = $accreditationRequest->committeeReport;
        if (! $report) {
            abort(404, 'التقرير غير موجود.');
        }

        $accreditationRequest->load([
            'program.department.college.university.officer',
            'programCoordinator',
            'committee.chairEvaluator.user',
        ]);

        $program = $accreditationRequest->program;
        $department = $program->department;
        $college = $department->college;
        $university = $college->university;

        $committee = $accreditationRequest->committee;

        // Load all standards with sub-standards and indicators
        $standards = Standard::with(['subStandards.indicators'])
            ->orderBy('id')
            ->get();

        // Load all final scores keyed by indicator_id.
        $savedScores = ReportScore::where('report_id', $report->id)
            ->where('score_type', 'final')
            ->pluck('score', 'indicator_id');

        $savedFormData = $report->form6_final_data ?? [];

        // Build members data for signatures (form_type = 'form_6_final' for stage 8)
        $membersData = [];

        // 1. Get Chair — look for latest stage8 approved record; chair has no approval_id entry
        //    so we fall back to a NULL-approval_id signature created after stage6 is done.
        $chairEvaluator = $committee?->chairEvaluator;
        if ($chairEvaluator) {
            // Chair signs via a NULL-approval_id record saved by StageEightController
            $chairSig = ReportSignature::where('report_id', $report->id)
                ->whereNull('approval_id')
                ->where('form_type', 'form_6_final')
                ->latest()
                ->first();

            $membersData[] = [
                'name' => $chairEvaluator->user->name,
                'signature_path' => $chairSig?->signature_path,
                'is_chair' => true,
            ];
        }

        // 2. Get Members — use stage8 CommitteeApproval records
        $members = $committee ? $committee->activeMembers->filter(fn ($m) => $m->evaluator_id !== $committee->chair_evaluator_id) : collect();

        foreach ($members as $member) {
            // Find the latest stage8 approved record for this member
            $latestApproval = CommitteeApproval::where('report_id', $report->id)
                ->where('member_id', $member->evaluator_id)
                ->where('review_round', 'stage8')
                ->where('status', 'approved')
                ->latest()
                ->first();

            $sigPath = null;
            if ($latestApproval) {
                $sig = ReportSignature::where('approval_id', $latestApproval->id)
                    ->where('form_type', 'form_6_final')
                    ->latest()
                    ->first();
                $sigPath = $sig?->signature_path;
            }

            $membersData[] = [
                'name' => $member->evaluator->user->name,
                'signature_path' => $sigPath,
                'is_chair' => false,
            ];
        }

        return Pdf::view('print_templates.rubrics_report_template', [
            'accreditationRequest' => $accreditationRequest,
            'report' => $report,
            'program' => $program,
            'department' => $department,
            'college' => $college,
            'university' => $university,
            'standards' => $standards,
            'savedScores' => $savedScores,
            'savedFormData' => $savedFormData,
            'membersData' => $membersData,
            'isPrint' => true,
            'isFinal' => true,
        ])
            ->format('a4')
            ->name('Final_Assessment_Metrics_req_'.$accreditationRequest->id.'.pdf')
            ->download();
    }

    /**
     * Print Stage Six Evaluators Committee Final Report (Form 7) as PDF.
     */
    public function printFinalReport(AccreditationRequest $accreditationRequest)
    {
        $report = $accreditationRequest->committeeReport;
        if (! $report) {
            abort(404, 'التقرير غير موجود.');
        }

        $accreditationRequest->load([
            'program.department.college.university.officer',
            'programCoordinator',
            'committee.chairEvaluator.user',
        ]);

        $program = $accreditationRequest->program;
        $department = $program->department;
        $college = $department->college;
        $university = $college->university;

        $committee = $accreditationRequest->committee;

        // Detect whether called from Stage 8 route to use the correct signatures
        $isStage8Route = request()->routeIs('requests.stage_eight.*');
        $reviewRound = $isStage8Route ? 'stage8' : 'stage6';
        $chairFormType = $isStage8Route ? 'form_6_final' : 'form_6_initial';
        $memberFormType = $isStage8Route ? 'form_6_final' : 'form_6_initial';

        // Build members data for the table/signatures
        $membersData = [];

        // 1. Get Chair
        $chairEvaluator = $committee?->chairEvaluator;
        if ($chairEvaluator) {
            $chairSig = ReportSignature::where('report_id', $report->id)
                ->whereNull('approval_id')
                ->where('form_type', $chairFormType)
                ->latest()
                ->first();

            $membersData[] = [
                'name' => $chairEvaluator->user->name,
                'signature_path' => $chairSig?->signature_path,
                'is_chair' => true,
            ];
        }

        // 2. Add Members
        $members = $committee ? $committee->activeMembers->filter(fn ($m) => $m->evaluator_id !== $committee->chair_evaluator_id) : collect();

        foreach ($members as $member) {
            $latestApproval = CommitteeApproval::where('report_id', $report->id)
                ->where('member_id', $member->evaluator_id)
                ->where('review_round', $reviewRound)
                ->where('status', 'approved')
                ->latest()
                ->first();

            $sigPath = null;
            if ($latestApproval) {
                $sig = ReportSignature::where('approval_id', $latestApproval->id)
                    ->where('form_type', $memberFormType)
                    ->latest()
                    ->first();
                $sigPath = $sig?->signature_path;
            }

            $membersData[] = [
                'name' => $member->evaluator->user->name,
                'signature_path' => $sigPath,
                'is_chair' => false,
            ];
        }

        // Use final scores for Stage 8, initial scores for Stage 6
        $scoreType = $isStage8Route ? 'final' : 'Initial';
        $standardsScores = $this->calculateStandardsScores($report->id, $scoreType);

        // Build detailed sub-standards data for the assessment table (Section 2 of Form 7).
        $detailedStandards = $this->buildDetailedStandards($report);

        return Pdf::view('print_templates.final_report_template', [
            'accreditationRequest' => $accreditationRequest,
            'report' => $report,
            'program' => $program,
            'department' => $department,
            'college' => $college,
            'university' => $university,
            'membersData' => $membersData,
            'standardsScores' => $standardsScores,
            'detailedStandards' => $detailedStandards,
            'isPrint' => true,
        ])
            ->format('a4')
            ->name('Final_Report_req_'.$accreditationRequest->id.'.pdf')
            ->download();
    }

    /**
     * Calculate per-standard scores from report_scores (initial type, scores 1-5 only).
     */
    private function calculateStandardsScores(int $reportId, string $scoreType = 'Initial'): array
    {
        $standards = Standard::with(['subStandards.indicators'])->orderBy('id')->get();

        // Load all scores for this report keyed by indicator_id.
        $scores = ReportScore::where('report_id', $reportId)
            ->where('score_type', $scoreType)
            ->pluck('score', 'indicator_id');

        $standardRows = [];
        $grandSum = 0;
        $grandCount = 0;

        foreach ($standards as $standard) {
            $stdSum = 0;
            $stdCount = 0;
            $hasNullIndicators = false;

            foreach ($standard->subStandards as $sub) {
                foreach ($sub->indicators as $indicator) {
                    $score = $scores->get($indicator->id);

                    if (is_null($score)) {
                        $hasNullIndicators = true;
                    } elseif ($score >= 1 && $score <= 5) {
                        $stdSum += $score;
                        $stdCount += 1;
                    }
                }
            }

            $stdAverage = $stdCount > 0 ? round($stdSum / $stdCount, 2) : null;

            $standardRows[] = [
                'id' => $standard->id,
                'name' => $standard->name,
                'sum' => $stdSum,
                'count' => $stdCount,
                'average' => $stdAverage,
                'has_null_indicators' => $hasNullIndicators,
            ];

            $grandSum += $stdSum;
            $grandCount += $stdCount;
        }

        $grandAverage = $grandCount > 0 ? round($grandSum / $grandCount, 2) : null;

        [$finalGrade, $achievementLevel] = $this->resolveGradeAndLevel($grandAverage);

        return [
            'standards' => $standardRows,
            'total' => [
                'sum' => $grandSum,
                'count' => $grandCount,
                'average' => $grandAverage,
            ],
            'final_grade' => $finalGrade,
            'achievement_level' => $achievementLevel,
        ];
    }

    /**
     * Build detailed standards data for Form 7 assessment table.
     */
    private function buildDetailedStandards(CommitteeReport $report): array
    {
        $standards = Standard::with(['subStandards.indicators'])->orderBy('id')->get();

        // Load all initial scores for this report keyed by indicator_id.
        $scores = ReportScore::where('report_id', $report->id)
            ->where('score_type', 'Initial')
            ->pluck('score', 'indicator_id');

        $formData = $report->form6_initial_data ?? [];
        $stdComments = $formData['standards'] ?? [];

        $result = [];

        foreach ($standards as $stdIndex => $standard) {
            $subRows = [];
            $stdCommentBlock = $stdComments[(string) $standard->id] ?? [];

            $allStrengths = $stdCommentBlock['strengths'] ?? [];
            $allImprovements = $stdCommentBlock['improvements'] ?? [];

            foreach ($standard->subStandards as $subStandard) {
                $subSum = 0;
                $subCount = 0;

                foreach ($subStandard->indicators as $indicator) {
                    $score = $scores->get($indicator->id);
                    if ($score !== null && $score >= 1 && $score <= 5) {
                        $subSum += $score;
                        $subCount += 1;
                    }
                }

                $subAverage = $subCount > 0 ? round($subSum / $subCount, 2) : null;

                $subStrengths = array_values(array_filter($allStrengths, fn ($p) => (string) ($p['subId'] ?? '') === (string) $subStandard->id));
                $subImprovements = array_values(array_filter($allImprovements, fn ($p) => (string) ($p['subId'] ?? '') === (string) $subStandard->id));

                $subRows[] = [
                    'id' => $subStandard->id,
                    'number' => $subStandard->number ?? ($stdIndex + 1),
                    'name' => $subStandard->name,
                    'average' => $subAverage,
                    'strengths' => array_column($subStrengths, 'text'),
                    'improvements' => array_column($subImprovements, 'text'),
                ];
            }

            $result[] = [
                'id' => $standard->id,
                'number' => $standard->number ?? ($stdIndex + 1),
                'name' => $standard->name,
                'subs' => $subRows,
            ];
        }

        return $result;
    }

    /**
     * Resolve the final grade (1-5) and Arabic achievement level label
     */
    private function resolveGradeAndLevel(?float $average): array
    {
        if ($average === null) {
            return [null, '—'];
        }

        if ($average >= 4.5) {
            return [5, 'محقق بامتياز'];
        }

        if ($average >= 3.5) {
            return [4, 'محقق بإتقان'];
        }

        if ($average >= 2.5) {
            return [3, 'محقق'];
        }

        if ($average >= 1.5) {
            return [2, 'محقق جزئياً'];
        }

        return [1, 'غير محقق'];
    }

    /**
     * Print Stage Six Recommendations Letter (Form 8) as PDF.
     */
    public function printRecommendationsLetter(AccreditationRequest $accreditationRequest)
    {
        $report = $accreditationRequest->committeeReport;
        if (! $report) {
            abort(404, 'التقرير غير موجود.');
        }

        $accreditationRequest->load([
            'program.department.college.university.officer',
            'programCoordinator',
        ]);

        $program = $accreditationRequest->program;
        $department = $program->department;
        $college = $department->college;
        $university = $college->university;

        // Build detailed standards data for the assessment table.
        $detailedStandards = $this->buildDetailedStandards($report);

        return Pdf::view('print_templates.recommendations_letter_template', [
            'accreditationRequest' => $accreditationRequest,
            'report' => $report,
            'program' => $program,
            'department' => $department,
            'college' => $college,
            'university' => $university,
            'detailedStandards' => $detailedStandards,
            'isPrint' => true,
        ])
            ->format('a4')
            ->name('Recommendations_Letter_req_'.$accreditationRequest->id.'.pdf')
            ->download();
    }

    /**
     * Print Stage Seven Response to Recommendations (Form 9) as PDF.
     */
    public function printFormNine(AccreditationRequest $accreditationRequest)
    {
        $user = request()->user();
        if (! $user) {
            abort(401);
        }

        $allowed = match ($user->role) {
            'accreditation_officer', 'council_secretariat' => true,
            'program_coordinator' => $accreditationRequest->program_coord_id === $user->id,
            'council_coordinator' => $accreditationRequest->council_coord_id === $user->id,
            'evaluator' => $accreditationRequest->committee && $accreditationRequest->committee->members()
                ->where('evaluator_id', $user->evaluator->id ?? 0)
                ->where('member_status', 'accepted')
                ->exists(),
            default => false,
        };

        if (! $allowed) {
            abort(403, 'غير مصرح لك بالوصول إلى هذه الملفات.');
        }

        $report = $accreditationRequest->committeeReport;
        if (! $report) {
            abort(404, 'التقرير غير موجود.');
        }

        $accreditationRequest->load([
            'program.department.college.university.officer',
            'programCoordinator',
        ]);

        $program = $accreditationRequest->program;
        $department = $program->department;
        $college = $department->college;
        $university = $college->university;

        // Build detailed standards and improvements
        $detailedStandards = $this->buildDetailedStandards($report);

        // Fetch form9_data responses
        $savedForm9Data = $report->form9_data ?? [];
        $savedBySubId = collect($savedForm9Data)->keyBy('sub_id');

        // Augment detailedStandards with the institution's responses
        foreach ($detailedStandards as &$std) {
            foreach ($std['subs'] as &$sub) {
                $saved = $savedBySubId->get($sub['id']);
                $sub['decision'] = $saved ? $saved['decision'] : null;
                $sub['rejection_points'] = ($saved && ! empty($saved['rejection_points'])) ? $saved['rejection_points'] : [];
            }
        }

        return Pdf::view('print_templates.form_nine_response_template', [
            'accreditationRequest' => $accreditationRequest,
            'report' => $report,
            'program' => $program,
            'department' => $department,
            'college' => $college,
            'university' => $university,
            'detailedStandards' => $detailedStandards,
            'isPrint' => true,
        ])
            ->format('a4')
            ->name('Form9_Response_req_'.$accreditationRequest->id.'.pdf')
            ->download();
    }

    /**
     * Print Stage Eight Final Decision (Form 10) as PDF.
     */
    public function printFinalDecision(AccreditationRequest $accreditationRequest)
    {
        $report = $accreditationRequest->committeeReport;
        if (! $report) {
            abort(404, 'التقرير غير موجود.');
        }

        $program = $accreditationRequest->program;
        $department = $program->department;
        $college = $department->college;
        $university = $college->university;

        $committee = $accreditationRequest->committee;

        // Calculate scores to get average and achievement level using final scores
        $standardsScores = $this->calculateStandardsScores($report->id, 'final');
        $grandAverage = $standardsScores['total']['average'];
        $achievementLevel = $standardsScores['achievement_level'];

        // Build members data for signatures (form_type = 'form_10')
        $membersData = [];

        // 1. Get Chair (Signature where approval_id is NULL)
        $chairEvaluator = $committee->chairEvaluator;
        if ($chairEvaluator) {
            $chairSig = ReportSignature::where('report_id', $report->id)
                ->whereNull('approval_id')
                ->where('form_type', 'form_10')
                ->latest()
                ->first();

            $membersData[] = [
                'name' => $chairEvaluator->user->name,
                'signature_path' => $chairSig?->signature_path,
                'signed_at' => $chairSig?->created_at,
                'is_chair' => true,
            ];
        }

        // 2. Add Members (Latest approved signature for stage8)
        $members = $committee->activeMembers->filter(fn ($m) => $m->evaluator_id !== $committee->chair_evaluator_id);

        foreach ($members as $member) {
            $latestApproval = CommitteeApproval::where('report_id', $report->id)
                ->where('member_id', $member->evaluator_id)
                ->where('review_round', 'stage8')
                ->where('status', 'approved')
                ->latest()
                ->first();

            $sigPath = null;
            $signedAt = null;
            if ($latestApproval) {
                $sig = ReportSignature::where('approval_id', $latestApproval->id)
                    ->where('form_type', 'form_10')
                    ->latest()
                    ->first();
                $sigPath = $sig?->signature_path;
                $signedAt = $sig?->created_at;
            }

            $membersData[] = [
                'name' => $member->evaluator->user->name,
                'signature_path' => $sigPath,
                'signed_at' => $signedAt,
                'is_chair' => false,
            ];
        }

        return Pdf::view('print_templates.final_decision_template', compact(
            'accreditationRequest',
            'program',
            'department',
            'college',
            'university',
            'grandAverage',
            'achievementLevel',
            'membersData'
        ))
            ->format('a4')
            ->name('Final_Decision_req_'.$accreditationRequest->id.'.pdf')
            ->download();
    }

    /**
     * Sanitize a string for use as a filename.
     */
    private function safeName(string $name): string
    {
        return str_replace(['/', '\\', ':', '*', '?', '"', '<', '>', '|'], '_', $name);
    }
}
