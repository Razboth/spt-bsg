<?php

namespace App\Http\Requests\Setting;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @property string $phone_number
 * @property string $address
 * @property string $name
 * @property string $fax
 * @property string $email
 * @property string $website
 * @property string $description
 */
class AppCompanyRequest extends FormRequest
{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => 'required',
            'address' => 'required',
            'phone_number' => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama perusahaan tidak boleh kosong',
            'address.required' => 'Alamat perusahaan tidak boleh kosong',
            'phone_number.required' => 'Nomor telepon perusahaan tidak boleh kosong',
        ];
    }
}
