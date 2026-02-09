<?php

namespace App\Http\Requests\Setting;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;

/**
 * @property string $id
 * @property string $parent_id
 * @property string $app_master_category_code
 * @property string $secret_id
 * @property string $name
 * @property string $parameter
 * @property string $code
 * @property string $description
 * @property string $order
 * @property string $status
 */

class AppMasterDataRequest extends FormRequest
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
            'parent_id' => 'required',
            'name' => 'required|string|max:255',
            'code' => 'required',
            'order' => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'parent_id.required' => 'Induk harus diisi',
            'name.required' => 'Nama harus diisi',
            'code.required' => 'Kode harus diisi',
            'order.required' => 'Urutan harus diisi',
        ];
    }

    protected function failedValidation(Validator $validator)
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
