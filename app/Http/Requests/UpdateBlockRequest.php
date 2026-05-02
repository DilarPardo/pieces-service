<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateBlockRequest extends FormRequest
{

    public function authorize(): bool 
    { 
        
        return true; 
    
    }

    public function rules(): array
    {
        $blockId = $this->route('block')->id;

        return [
            'code'       => 'sometimes|unique:blocks,code,' . $blockId,
            'name'       => 'sometimes|string|max:255',
            'status'     => 'sometimes|in:active,inactive',
            'project_id' => 'sometimes|exists:projects,id',
        ];
    }

}
