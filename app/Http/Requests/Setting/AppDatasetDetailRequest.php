<?php

namespace App\Http\Requests\Setting;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;

/**
 * @property string $id
 * @property string $dataset_id
 * @property string $title
 * @property string $code
 * @property string $example
 * @property string $description
 * @property string $created_by
 * @property string $updated_by
 */

class AppDatasetDetailRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'dataset_id' => 'required',
            'title' => 'required',
            'code' => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'dataset_id.required' => 'Dataset wajib diisi',
            'title.required' => 'Judul wajib diisi',
            'code.required' => 'Penggunaan wajib diisi',
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
