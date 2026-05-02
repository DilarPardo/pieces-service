<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProjectRequest extends FormRequest
{
    
    public function authorize(): bool 
    { 
        
        return true; 
    
    }

    public function rules(): array
    {
    
        $projectId = $this->route('project')->id;

        return [
            'code'        => 'sometimes|unique:projects,code,' . $projectId,
            'name'        => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'status'      => 'sometimes|in:active,inactive',
            'start_date'  => 'nullable|date',
            'end_date'    => 'nullable|date|after_or_equal:start_date',
        ];

    }

}
