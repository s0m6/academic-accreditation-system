@extends('partials.app')

@section('title', 'الشهادات المعتمدة')
@section('title2', 'الشهادات المعتمدة')
@section('description', 'سجل بجميع الشهادات المعتمدة التي تم إصدارها للجامعات والبرامج.')

@section('content')
<div class="card p-6 bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800">
    <div class="overflow-x-auto">
        <table class="w-full text-right text-sm text-slate-600 dark:text-slate-300">
            <thead class="bg-slate-50 dark:bg-slate-800/50 text-slate-900 dark:text-slate-100 text-xs font-bold uppercase">
                <tr>
                    <th scope="col" class="px-6 py-4 rounded-s-xl">رقم الشهادة</th>
                    <th scope="col" class="px-6 py-4">الجهة (الجامعة/البرنامج)</th>
                    <th scope="col" class="px-6 py-4">تاريخ الإصدار</th>
                    <th scope="col" class="px-6 py-4">تاريخ الانتهاء</th>
                    <th scope="col" class="px-6 py-4">الحالة</th>
                    <th scope="col" class="px-6 py-4 rounded-e-xl text-center">الإجراءات</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 dark:divide-slate-700/50">
                @forelse ($certificates as $certificate)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/20 transition-colors">
                        <td class="px-6 py-4 font-bold text-slate-900 dark:text-slate-100" dir="ltr" style="text-align: right;">
                            {{ $certificate->certificate_number }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-bold text-slate-900 dark:text-slate-100">
                                {{ $certificate->finalDecision->accreditationRequest->program->department->college->university->name ?? 'غير محدد' }}
                            </div>
                            <div class="text-xs text-slate-500 mt-1">
                                {{ $certificate->finalDecision->accreditationRequest->program->program_name ?? 'اعتماد مؤسسي' }}
                            </div>
                        </td>
                        <td class="px-6 py-4" dir="ltr" style="text-align: right;">
                            {{ \Carbon\Carbon::parse($certificate->certificate_data['issued_at_raw'] ?? $certificate->created_at)->format('Y-m-d') }}
                        </td>
                        <td class="px-6 py-4" dir="ltr" style="text-align: right;">
                            @if(isset($certificate->certificate_data['expires_at_raw']))
                                {{ \Carbon\Carbon::parse($certificate->certificate_data['expires_at_raw'])->format('Y-m-d') }}
                            @else
                                <span class="text-slate-400 italic">غير محدد</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($certificate->isValid())
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400 border border-green-200 dark:border-green-800">
                                    <i class="fa-solid fa-certificate me-1.5"></i> سارية المفعول
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400 border border-red-200 dark:border-red-800">
                                    <i class="fa-solid fa-triangle-exclamation me-1.5"></i> منتهية / ملغية
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            <a href="{{ route('certificate.show', $certificate->certificate_number) }}" target="_blank" 
                               class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white text-xs font-bold rounded-xl hover:bg-primary-dark transition-all shadow-sm">
                                <i class="fa-regular fa-eye"></i> استعراض
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-slate-500">
                            <div class="flex flex-col items-center justify-center">
                                <i class="fa-solid fa-award text-4xl text-slate-300 dark:text-slate-600 mb-3"></i>
                                <p class="text-base font-semibold">لا توجد شهادات مصدرة حتى الآن</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="mt-6">
        {{ $certificates->links() }}
    </div>
</div>
@endsection
