<?php

namespace App\Http\Requests\PSA;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @property string $id
 * @property string $plan_id
 * @property string $agent_id
 * @property string $customer_id
 * @property string $title
 * @property string $date
 * @property string $time
 * @property string $product_id
 * @property string $type_id
 * @property string $note
 * @property string $filename
 * @property int $closing_amount
 * @property string $account_number
 * @property string $approved_by
 * @property string $approved_status
 * @property string $approved_date
 * @property string $approved_note
 * @property float $latitude
 * @property float $longitude
 * @property string $address
 * @property string $created_by
 * @property string $updated_by
 */

class RealizationRequest extends FormRequest
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
            'plan_id' => 'required',
            'date' => 'required',
            'time' => 'required',
            'product_id' => 'required',
            'type_id' => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'plan_id.required' => 'Rencana tidak boleh kosong',
            'date.required' => 'Tanggal tidak boleh kosong',
            'time.required' => 'Waktu tidak boleh kosong',
            'product_id.required' => 'Produk tidak boleh kosong',
            'type_id.required' => 'Tipe tidak boleh kosong',
        ];
    }
}
