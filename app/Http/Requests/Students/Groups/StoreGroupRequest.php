<?php

namespace App\Http\Requests\Students\Groups;

use Illuminate\Foundation\Http\FormRequest;

class StoreGroupRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
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
            'name' => 'required',
            'department_id' => 'required',
            'university_id' => 'required',
            'kankor_year' => 'required',
            'description' => 'required',
            'gender' => 'required',
        ];
    }
}
