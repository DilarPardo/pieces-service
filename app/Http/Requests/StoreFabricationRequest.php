<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreFabricationRequest extends FormRequest
{

    public function authorize(): bool 
    { 
        
        return true; 
    
    }

    public function rules(): array
    {
        return [
            'piece_id'     => 'required|exists:pieces,id',
            'real_weight'  => 'required|numeric|min:0',
            'status'       => 'required|in:Pendiente,Fabricada',
            'observations' => 'nullable|string',
        ];
    }

}
