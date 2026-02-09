<?php

namespace App\Http\Requests\Setting;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @property string $type
 * @property string $dataset_id
 * @property string $page_id
 * @property string $name
 * @property string $code
 * @property string $content
 * @property string $margin_bottom
 * @property string $margin_top
 * @property string $margin_left
 * @property string $margin_right
 * @property string $created_by
 * @property string $updated_by
 */

class AppMasterTemplateRequest extends FormRequest
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
            'type' => 'required',
            'name' => 'required',
            'code' => 'required',
        ];
    }

    //generate message indonesian
    public function messages(): array
    {
        return [
            'type.required' => 'Jenis template wajib diisi',
            'name.required' => 'Nama template wajib diisi',
            'code.required' => 'Kode template wajib diisi',
        ];
    }
}
