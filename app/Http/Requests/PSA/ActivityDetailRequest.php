<?php

namespace App\Http\Requests\PSA;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;

/**
 * @property string id
 * @property string type
 * @property string activity_id
 * @property string type_id
 * @property string plan_date
 * @property string plan_time
 * @property string plan_note
 * @property string plan_filename
 * @property string realization_date
 * @property string realization_time
 * @property string realization_note
 * @property string realization_filename
 * @property int closing_amount
 * @property int customer_balance
 * @property string account_number
 * @property string cif
 * @property string approved_by
 * @property string approved_status
 * @property string approved_date
 * @property string approved_note
 * @property string approved_2_by
 * @property string approved_2_status
 * @property string approved_2_date
 * @property string approved_2_note
 * @property float latitude
 * @property float longitude
 * @property string address
 * @property string created_by
 * @property string updated_by
 */


class ActivityDetailRequest extends FormRequest
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
            'activity_id' => 'required',
            'type_id' => 'required',
            'plan_filename' => 'mimes:jpg,jpeg,png|max:2048',
            'plan_document' => 'mimes:pdf,xlsx,xls,jpg,jpeg,png|max:2048',
            'realization_filename' => 'mimes:jpg,jpeg,png|max:2048',
            'realization_document' => 'mimes:pdf,xlsx,xls,jpg,jpeg,png|max:2048',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'activity_id.required' => 'Field aktifitas tidak boleh kosong!',
            'type_id.required' => 'Field tipe tidak boleh kosong!',
            'plan_filename.mimes' => 'File harus berupa gambar',
            'plan_filename.max' => 'Ukuran file maksimal 2 MB',
            'plan_document.mimes' => 'File harus berupa pdf, xlsx, xls, jpg, jpeg, png',
            'plan_document.max' => 'Ukuran file maksimal 2 MB',
            'realization_filename.mimes' => 'File harus berupa gambar',
            'realization_filename.max' => 'Ukuran file maksimal 2 MB',
            'realization_document.mimes' => 'File harus berupa pdf, xlsx, xls, jpg, jpeg, png',
            'realization_document.max' => 'Ukuran file maksimal 2 MB',
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
