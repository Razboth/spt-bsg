<?php

namespace App\Http\Requests\PSA;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;

/**
 * @property string id
 * @property string instructor_id
 * @property string agent_id
 * @property string date
 * @property string goal
 * @property string reality
 * @property string option
 * @property string created_by
 * @property string updated_by
 */

class CoachingRequest extends FormRequest
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
            'instructor_id' => 'required',
            'agent_id' => 'required',
            'date' => 'required',
            'goal' => 'required',
            'reality' => 'required',
            'option' => 'required',
        ];
    }

    //generate message with indonesian language
    public function messages(): array
    {
        return [
            'instructor_id.required' => 'Instruktur harus diisi',
            'agent_id.required' => 'Peserta harus diisi',
            'date.required' => 'Tanggal harus diisi',
            'goal.required' => 'Tujuan harus diisi',
            'reality.required' => 'Kenyataan harus diisi',
            'option.required' => 'Opsi harus diisi',
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
