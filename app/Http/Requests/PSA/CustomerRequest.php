<?php

namespace App\Http\Requests\PSA;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;

/**
 * @property string id
 * @property string category_id
 * @property string code
 * @property string name
 * @property string pic
 * @property string email
 * @property string phone_number
 * @property string identity_number
 * @property string address
 * @property string description
 * @property string image
 * @property string status
 * @property string created_by
 * @property string updated_by
 */
class CustomerRequest extends FormRequest
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
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'category_id' => 'required',
            'code' => 'required',
            'name' => 'required',
            'pic' => 'nullable',
            'phone_number' => 'required',
            'email' => 'nullable|email',
            'address' => 'nullable',
            'description' => 'nullable',
            'image' => 'max:2048',
        ];
    }

    /**
     * Get the validation messages that apply to the request.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Nama tidak boleh kosong',
            'code.required' => 'Kode tidak boleh kosong',
            'category_id.required' => 'Kategori tidak boleh kosong',
            'phone_number.required' => 'Nomor Telepon tidak boleh kosong',
            'image.max' => 'Ukuran file terlalu besar maksimal 2MB',
        ];
    }
}
