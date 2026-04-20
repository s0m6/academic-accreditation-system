<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEvaluatorRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->role === 'council_secretariat';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $evaluatorId = $this->route('evaluator')->id ?? null;
        $userId = $this->route('evaluator')->user_id ?? null;

        return [
            // Personal / user data
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId)],
            'phone' => ['nullable', 'string', 'max:20'],
            'mobile' => ['required', 'string', 'max:20'],

            // Evaluator profile fields
            'city_id' => ['required', 'integer', 'exists:cities,id'],
            'general_specialty' => ['required', 'string', 'max:255'],
            'detailed_specialty' => ['required', 'string', 'max:255'],
            'academic_rank' => ['required', Rule::in(['Professor', 'Associate Professor', 'Assistant Professor', 'Lecturer', 'Expert'])],
            'current_university_id' => ['nullable', 'integer', 'exists:universities,id'],

            // Conflicts of interest
            'conflicts' => ['nullable', 'array'],
            'conflicts.*.university_id' => ['required_with:conflicts', 'integer', 'exists:universities,id'],
            'conflicts.*.conflict_text' => ['required_with:conflicts', 'string', 'max:1000'],

            // Attachments
            'attachments' => ['nullable', 'array'],
            'attachments.*.name' => ['required_with:attachments', 'string', 'max:255'],
            'attachments.*.file' => ['required_with:attachments', 'file', 'mimes:pdf', 'max:10240'],

            // Deleted attachments
            'deleted_attachments' => ['nullable', 'array'],
            'deleted_attachments.*' => ['integer', 'exists:evaluator_attachments,id'],
        ];
    }

    /**
     * Get custom attribute names for validation error messages.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => 'الاسم الكامل',
            'email' => 'البريد الإلكتروني',
            'phone' => 'رقم الهاتف',
            'mobile' => 'رقم الجوال',
            'city_id' => 'المدينة',
            'general_specialty' => 'التخصص العام',
            'detailed_specialty' => 'التخصص الدقيق',
            'academic_rank' => 'الدرجة العلمية',
            'current_university_id' => 'الجامعة الحالية',
            'deleted_attachments' => 'المرفقات المحذوفة',
        ];
    }
}
