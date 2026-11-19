<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class JobVacancyRequest extends FormRequest
{
   public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'judul' => ['required', 'string', 'max:150'],
            'id_kategori' => ['required', 'exists:kategori_pekerjaan,id_kategori'],
            'lokasi' => ['required', 'string', 'max:255'],
            'upah' => ['required', 'integer', 'min:1'],
            'waktu_pekerjaan' => ['nullable', 'date', 'after_or_equal:today'],
            'deskripsi' => ['required', 'string', 'max:1000'],
        ];
    }

    public function attributes(): array
    {
        return [
            'judul' => 'judul pekerjaan',
            'id_kategori' => 'kategori',
            'lokasi' => 'lokasi & patokan',
            'upah' => 'nominal upah',
            'waktu_pekerjaan' => 'tanggal pelaksanaan',
            'deskripsi' => 'catatan / instruksi',
        ];
    }

    public function messages(): array
    {
        return [
            'required' => 'Kolom :attribute wajib diisi.',
            'max' => 'Kolom :attribute maksimal :max karakter.',
            'date' => 'Kolom :attribute harus berupa tanggal yang valid.',
            'integer' => 'Kolom :attribute harus berupa angka bulat.',
            'min' => 'Kolom :attribute minimal Rp1.',
            'waktu_pekerjaan.after_or_equal' => 'Tanggal pelaksanaan tidak boleh sebelum hari ini.',
            'exists' => 'Pilihan :attribute tidak valid.',
        ];
    }
}
