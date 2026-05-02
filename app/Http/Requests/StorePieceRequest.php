<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StorePieceRequest extends FormRequest
{

    public function authorize(): bool 
    { 
        
        return true; 
    
    }

    public function rules(): array
    {
        return [
            'code'               => 'required|unique:pieces,code',
            'block_id'           => 'required|exists:blocks,id',
            'name'               => 'required|string|max:255',
            'theoretical_weight' => 'required|numeric|min:0.01', 
            'description'        => 'nullable|string'
        ];
    }

}
