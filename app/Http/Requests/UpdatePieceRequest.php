<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePieceRequest extends FormRequest
{

    public function authorize(): bool 
    { 
        
        return true; 
    
    }

    public function rules(): array
    {
        $pieceId = $this->route('piece')->id;

        return [
            'code'               => 'sometimes|unique:pieces,code,' . $pieceId,
            'name'               => 'sometimes|string|max:255',
            'theoretical_weight' => 'sometimes|numeric|min:0',
            'description'        => 'nullable|string',
            'block_id'           => 'sometimes|exists:blocks,id'
        ];
    }

}
