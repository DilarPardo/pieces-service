<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreBlockRequest extends FormRequest
{

    public function authorize(): bool 
    { 
        
        return true; 
    
    }

    public function rules(): array
    {
        return [
            'code'        => 'required|unique:blocks,code',
            'project_id'  => 'required|exists:projects,id',
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'status'      => 'nullable|in:active,inactive',
        ];
    }

}
