<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEvaluatorRequest extends FormRequest
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
        return [
            // Personal / user data
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
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
        ];
    }
}
