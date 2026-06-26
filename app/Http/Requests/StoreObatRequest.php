<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreObatRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Wajib diubah menjadi true
    }

    public function rules(): array
    {
        return [
            'nama_obat'      => 'required|string|max:255',
            'jenis_obat'     => 'required|string|in:Tablet,Vaksin,Sirup,Infus,Kapsul,Alkes',
            'stok'           => 'required|integer|min:1',
            'harga_beli'     => 'required|numeric|min:0.01',
            'tgl_kadaluarsa' => 'required|date',
        ];
    }
}
