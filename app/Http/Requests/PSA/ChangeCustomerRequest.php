<?php

namespace App\Http\Requests\PSA;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;

/**
 * @property string $id;
 * @property string $number;
 * @property string $date;
 * @property string $from_unit_id;
 * @property string $from_agent_id;
 * @property string $customer_id;
 * @property string $to_unit_id;
 * @property string $to_agent_id;
 * @property string $description;
 * @property string $approved_status;
 * @property string $approved_by;
 * @property string $approved_date;
 * @property string $approved_note;
 */

class ChangeCustomerRequest extends FormRequest
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
            'number' => 'required',
            'date' => 'required',
            'from_unit_id' => 'required',
            'from_agent_id' => 'required',
            'customer_id' => 'required',
            'to_unit_id' => 'required',
            'to_agent_id' => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'number.required' => 'Nomor tidak boleh kosong',
            'date.required' => 'Tanggal tidak boleh kosong',
            'from_unit_id.required' => 'Unit asal tidak boleh kosong',
            'from_agent_id.required' => 'Sales asal tidak boleh kosong',
            'customer_id.required' => 'Customer tidak boleh kosong',
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
