<?php

namespace App\Http\Requests\PSA;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;

/**
 * @property string $id
 * @property string $type
 * @property string $period_id
 * @property string $unit_id
 * @property string $product_id
 * @property int $total
 * @property string $created_by
 * @property string $updated_by
 */

class TargetUnitRequest extends FormRequest
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
            'type' => 'required',
            'period_id' => 'required',
            'unit_id' => 'required',
            'product_id' => 'required',
            'total' => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'type.required' => 'Tipe wajib diisi',
            'period_id.required' => 'Periode wajib diisi',
            'unit_id.required' => 'Unit wajib diisi',
            'product_id.required' => 'Produk wajib diisi',
            'total.required' => 'Total wajib diisi',
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
