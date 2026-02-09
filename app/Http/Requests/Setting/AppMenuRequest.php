<?php

namespace App\Http\Requests\Setting;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;

/**
 * @property integer $id
 * @property integer $parent_id
 * @property integer $app_modul_id
 * @property integer $app_sub_modul_id
 * @property string $name
 * @property string $target
 * @property string $description
 * @property string $icon
 * @property string $parameter
 * @property string $full_screen
 * @property string $order
 * @property string $status
 *
 */

class AppMenuRequest extends FormRequest
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
            'app_modul_id' => 'required',
            'app_sub_modul_id' => 'required',
            'name' => 'required',
            'target' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'app_modul_id.required' => 'Modul harus diisi',
            'app_sub_modul_id.required' => 'Sub Modul harus diisi',
            'name.required' => 'Nama harus diisi',
            'target.required' => 'Target harus diisi',
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
