<?php

namespace App\Http\Requests\PSA;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;

/**
 * @property string $id
 * @property string $setting_position_product_id
 * @property string $product_id
 * @property string $weight
 * @property string $created_by
 * @property string $updated_by
 */


class SettingPositionProductDetailRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'setting_position_product_id' => 'required',
            'product_id' => 'required',
            'weight' => 'required|gt:0',
        ];
    }

    public function messages(): array
    {
        return [
            'setting_position_product_id.required' => 'Setting posisi produk harus diisi',
            'product_id.required' => 'Produk harus diisi',
            'weight.required' => 'Bobot harus diisi',
            'weight.gt' => 'Bobot harus lebih besar dari 0',
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
