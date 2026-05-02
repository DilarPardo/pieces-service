<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    
    use SoftDeletes;

    protected $fillable = [
        'code', 'name', 'description', 'status', 'start_date', 'end_date', 'created_by'
    ];

    public function blocks(): HasMany
    {
        return $this->hasMany(Block::class);
    }

    protected function casts(): array 
    {

    return [
        'start_date' => 'date',
        'end_date'   => 'date',
    ];

    }

}
