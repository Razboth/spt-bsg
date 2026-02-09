<?php

namespace App\Http\Requests\PSA;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;

/**
 * @property string $id
 * @property string $agent_id
 * @property string $customer_id
 * @property string $product_id
 * @property string $month
 * @property string $year
 * @property int $value
 * @property string $description
 * @property string $approved_status
 * @property string $approved_date
 * @property string $approved_by
 * @property string $approved_note
 * @property string $created_by
 * @property string $updated_by
 */

class AgentBoosterRequest extends FormRequest
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
            'agent_id' => 'required',
            'customer_id' => 'required',
            'product_id' => 'required',
            'month' => 'required',
            'year' => 'required',
            'value' => 'required',
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
