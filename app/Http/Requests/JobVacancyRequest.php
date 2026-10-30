<?php

namespace App\Http\Requests;

use App\Enums\VacancyCategory;
use App\Enums\VacancyDuration;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class JobVacancyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * Authorization is handled by the JobVacancyPolicy in the controller.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'category' => ['required', Rule::enum(VacancyCategory::class)],
            'location' => ['required', 'string', 'max:255'],
            'work_date' => ['required', 'date', 'after_or_equal:today'],
            'duration' => ['required', Rule::enum(VacancyDuration::class)],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Get custom attribute names for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'title' => 'judul pekerjaan',
            'category' => 'kategori',
            'location' => 'lokasi & patokan',
            'work_date' => 'tanggal pelaksanaan',
            'duration' => 'estimasi durasi',
            'notes' => 'catatan tambahan',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'required' => 'Kolom :attribute wajib diisi.',
            'max' => 'Kolom :attribute maksimal :max karakter.',
            'date' => 'Kolom :attribute harus berupa tanggal yang valid.',
            'work_date.after_or_equal' => 'Tanggal pelaksanaan tidak boleh sebelum hari ini.',
            Enum::class => 'Pilihan :attribute tidak valid.',
            'enum' => 'Pilihan :attribute tidak valid.',
        ];
    }
}
