<?php

namespace App\Http\Requests\PSA;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @property string $id
 * @property string $name
 * @property string $nickname
 * @property string $employee_number
 * @property string $identity_number
 * @property string $birth_place
 * @property string $birth_date
 * @property string $phone_number
 * @property string $email
 * @property string $address
 * @property string $gender
 * @property string $status_id
 * @property string $type_id
 * @property string $rank_id
 * @property string $position_id
 * @property string $join_date
 * @property string $leave_date
 * @property string $identity_file
 * @property string $photo
 * @property string $leader_id
 * @property string $unit_id
 * @property string $activity_type
 * @property string $created_by
 * @property string $updated_by
 */

class AgentRequest extends FormRequest
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
            'status_id' => 'required',
            'type_id' => 'required',
            'rank_id' => 'required',
            'position_id' => 'required',
            'join_date' => 'required',
            'photo' => 'mimes:jpeg,jpg,png|max:2048'
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama tidak boleh kosong',
            'status_id.required' => 'Status tidak boleh kosong',
            'type_id.required' => 'Jenis Pegawai tidak boleh kosong',
            'rank_id.required' => 'Pangkat tidak boleh kosong',
            'position_id.required' => 'Jabatan tidak boleh kosong',
            'join_date.required' => 'Tanggal Masuk tidak boleh kosong',
            'photo.mimes' => 'Format file tidak sesuai',
            'photo.max' => 'Ukuran file terlalu besar',
        ];
    }
}
