<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StorePenilaianRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->isAdmin();
    }

    public function rules(): array
    {
        $kriteriaIds = \App\Models\Kriteria::pluck('id')->toArray();

        $rules = [];
        foreach ($kriteriaIds as $id) {
            $rules["penilaian.{$id}"] = ['required', 'exists:sub_kriteria,id'];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'penilaian.*.required' => 'Semua kriteria wajib dinilai.',
            'penilaian.*.exists'   => 'Sub kriteria yang dipilih tidak valid.',
        ];
    }

    public function attributes(): array
    {
        $attributes = [];
        $kriterias = \App\Models\Kriteria::pluck('nama_kriteria', 'id');

        foreach ($kriterias as $id => $nama) {
            $attributes["penilaian.{$id}"] = $nama;
        }

        return $attributes;
    }
}