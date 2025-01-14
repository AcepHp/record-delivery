<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDataRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'line' => 'required|string|max:255',
            'no_station' => 'required|string|max:255',
            'nama_station' => 'required|string|max:255',
            'nama_barang' => 'required|string|max:255',
            'kode_barang' => 'required|string|max:255',
        ];
    }
}
