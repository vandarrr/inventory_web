<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class updateKategoriProdukRequest extends FormRequest
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
            'nama_kategori' => 'required|unique:kategori_produks,nama_kategori,' . $this->kategori_produk->id,
        ];
    }

    public function messages(): array
    {
        return [
            'required' => 'Nama kategori harus diisi.',
            'unique' => 'Nama kategori sudah ada.',
        ];
    }
}
