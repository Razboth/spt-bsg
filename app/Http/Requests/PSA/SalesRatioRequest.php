<?php

namespace App\Http\Requests\PSA;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;

/**
 * @property string $id
 * @property string $date
 * @property string $unit_id
 * @property string $agent_id
 * @property string $customer_id
 * @property string $product_id
 * @property string $activity_detail_id
 * @property string $nominal
 * @property string $core_nominal
 * @property string $description
 * @property string $approved_by
 * @property string $approved_status
 * @property string $approved_date
 * @property string $approved_note
 * @property string $created_by
 * @property string $updated_by
 */

class SalesRatioRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'date' => 'required',
            'unit_id' => 'required',
            'agent_id' => 'required',
            'customer_id' => 'required',
            'product_id' => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'date.required' => 'Tanggal tidak boleh kosong',
            'unit_id.required' => 'Unit tidak boleh kosong',
            'agent_id.required' => 'Sales tidak boleh kosong',
            'customer_id.required' => 'Customer tidak boleh kosong',
            'product_id.required' => 'Produk tidak boleh kosong',
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
