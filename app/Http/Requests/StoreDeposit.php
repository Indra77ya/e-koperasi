<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDeposit extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'tipe_penyetor' => 'required|in:anggota,nasabah',
            'anggota' => 'required_if:tipe_penyetor,anggota|nullable|exists:anggota,id',
            'nasabah' => 'required_if:tipe_penyetor,nasabah|nullable|exists:nasabahs,id',
            'jumlah' => 'required|integer|min:1',
        ];
    }
}
