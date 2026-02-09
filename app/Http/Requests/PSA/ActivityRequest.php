<?php

namespace App\Http\Requests\PSA;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @property string $id
 * @property string $date
 * @property string $type
 * @property string $activity_type
 * @property string $number
 * @property string $agent_id
 * @property string $customer_id
 * @property string $product_id
 * @property string $sub_product_id
 * @property string $approved_by
 * @property string $approved_status
 * @property string $approved_date
 * @property string $approved_note
 * @property string $approved_2_by
 * @property string $approved_2_status
 * @property string $approved_2_date
 * @property string $approved_2_note
 * @property string $created_by
 * @property string $updated_by
 */

class ActivityRequest extends FormRequest
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
            'number' => 'required',
            'date' => 'required',
            'agent_id' => 'required',
            'customer_id' => 'required',
            'product_id' => 'required|sometimes',
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
            'type.required' => 'Field Tipe tidak boleh kosong',
            'number.required' => 'Field Nomor tidak boleh kosong',
            'date.required' => 'Field Tanggal tidak boleh kosong',
            'agent_id.required' => 'Field Agent tidak boleh kosong',
            'customer_id.required' => 'Field Customer tidak boleh kosong',
            'product_id.required' => 'Field Product tidak boleh kosong',
        ];
    }
}
