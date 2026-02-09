<?php

namespace App\Http\Requests\Setting;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;

/**
 * @property string id
 * @property string title
 * @property string code
 * @property string delimiter
 * @property string var1_type
 * @property string var1_value
 * @property string var2_type
 * @property string var2_value
 * @property string var3_type
 * @property string var3_value
 * @property string var4_type
 * @property string var4_value
 * @property string var5_type
 * @property string var5_value
 * @property string example
 * @property string description
 * @property string created_by
 * @property string updated_by
 */

class AppNumberFormatRequest extends FormRequest
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
            'title' => 'required',
            'code' => 'required',
            'delimiter' => 'required',
            'example' => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Judul harus diisi',
            'code.required' => 'Kode harus diisi',
            'delimiter.required' => 'Pemisah harus diisi',
            'example.required' => 'Contoh harus diisi',
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        if ($this->expectsJson()) {
            $errors = (new ValidationException($validator))->errors();
            throw new HttpResponseException(
                response()->json(['errors' => $errors], 200)
            );
        }

        parent::failedValidation($validator);
    }
}
