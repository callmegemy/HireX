<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCategoryRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules()
    {
        return [
            'name' => [
                'required',
                Rule::unique('job_categories', 'name')->ignore($this->id),
                'max:255',
            ],
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'The category name is required.',
            'name.unique' => 'This category name has already been taken.',
        ];
    }
}
