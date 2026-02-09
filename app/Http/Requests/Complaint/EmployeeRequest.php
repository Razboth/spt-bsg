<?php

namespace App\Http\Requests\Complaint;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;

/**
 * @property string $id
 * @property string $name
 * @property string $employee_number
 * @property string $gender
 * @property string $address
 * @property string $province_id
 * @property string $city_id
 * @property string $email
 * @property string $phone_number
 * @property string $position_id
 * @property string $employee_type_id
 * @property string $status_id
 * @property string $join_date
 * @property string $leave_date
 * @property string $photo
 * @property string $created_by
 * @property string $updated_by
 */

class EmployeeRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => 'required',
            'employee_number' => 'required',
            'position_id' => 'required',
            'employee_type_id' => 'required',
            'status_id' => 'required',
            'join_date' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Nama harus diisi',
            'employee_number.required' => 'Nomor Pegawai harus diisi',
            'position_id.required' => 'Jabatan harus diisi',
            'employee_type_id.required' => 'Tipe Pegawai harus diisi',
            'status_id.required' => 'Status harus diisi',
            'join_date.required' => 'Tanggal Masuk harus diisi',
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
