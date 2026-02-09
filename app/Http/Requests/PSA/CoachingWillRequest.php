<?php

namespace App\Http\Requests\Seam;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @property string $id
 * @property string $coaching_id
 * @property string $will
 * @property string $plan_date
 * @property string $action_date
 * @property int $realization
 * @property string $filename
 * @property string $note
 * @property string $validation_status
 * @property string $validation_note
 * @property string $validation_at
 * @property string $validation_by
 * @property string $created_by
 * @property string $updated_by
 */

class CoachingWillRequest extends FormRequest
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
            'coaching_id' => 'required',
            'will' => 'required',
            'plan_date' => 'required',
        ];
    }
}
