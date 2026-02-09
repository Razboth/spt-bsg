<?php

namespace App\Http\Requests\Setting;

use Illuminate\Foundation\Http\FormRequest;

class AppInfoRequest extends FormRequest
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
            'title' => 'required',
            'primary_color' => 'required',
            'light_primary_color' => 'required',
            'background_light_primary_color' => 'required',
            'year' => 'required',
            'app_version' => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Judul tidak boleh kosong',
            'primary_color.required' => 'Warna utama tidak boleh kosong',
            'light_primary_color.required' => 'Warna terang utama tidak boleh kosong',
            'background_light_primary_color.required' => 'Warna latar belakang terang utama tidak boleh kosong',
            'year.required' => 'Tahun tidak boleh kosong',
            'app_version.required' => 'Versi aplikasi tidak boleh kosong',
        ];
    }
}
