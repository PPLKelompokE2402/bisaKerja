<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class JobVacancyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
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
            'judul' => ['required', 'string', 'max:150'],
            'id_kategori' => ['required', 'exists:kategori_pekerjaan,id_kategori'],
            'lokasi' => ['required', 'string', 'max:255'],
            'waktu_pekerjaan' => ['nullable', 'date', 'after_or_equal:today'],
            'deskripsi' => ['required', 'string', 'max:1000'],
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
            'judul' => 'judul pekerjaan',
            'id_kategori' => 'kategori',
            'lokasi' => 'lokasi & patokan',
            'waktu_pekerjaan' => 'tanggal pelaksanaan',
            'deskripsi' => 'catatan / instruksi',
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
            'waktu_pekerjaan.after_or_equal' => 'Tanggal pelaksanaan tidak boleh sebelum hari ini.',
            'exists' => 'Pilihan :attribute tidak valid.',
        ];
    }
}
