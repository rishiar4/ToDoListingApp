<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use Illuminate\Validation\Rule;

class CreateToDoRecordRequest extends Request
{

    /**
     * Determine if authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => [
                'required',
                Rule::unique('to_dos')->whereNull('deleted_at'),
            ],
        ];
    }

    public function messages()
    {
        return [
            'name.unique' => 'The task name has already been taken.',
        ];
    }
}
