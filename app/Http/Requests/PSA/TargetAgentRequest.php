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
 * @property string $agent_id
 * @property string $target_period_id
 * @property string $product_id
 * @property int $total
 * @property string $created_by
 * @property string $updated_by
 */

class TargetAgentRequest extends FormRequest
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
            'target_period_id' => 'required',
            'agent_id' => 'required',
            'product_id' => 'required',
            'total' => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'type.required' => 'Tipe wajib diisi',
            'target_period_id.required' => 'Periode wajib diisi',
            'agent_id.required' => 'Sales wajib diisi',
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
