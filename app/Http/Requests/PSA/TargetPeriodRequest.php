<?php

namespace App\Http\Requests\PSA;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;

/**
 * @property string id
 * @property string name
 * @property string start_date
 * @property string end_date
 * @property string note
 * @property string filename
 * @property string created_by
 * @property string updated_by
 */

class TargetPeriodRequest extends FormRequest
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
            'name' => 'required',
            'start_date' => 'required',
            'end_date' => 'required'
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama periode target tidak boleh kosong',
            'start_date.required' => 'Tanggal mulai periode target tidak boleh kosong',
            'end_date.required' => 'Tanggal akhir periode target tidak boleh kosong'
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
