<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Piece extends Model
{
    
    use SoftDeletes;

    protected $fillable = [
        'code', 'block_id', 'name', 'description', 'theoretical_weight'
    ];

    public function block(): BelongsTo
    {
        return $this->belongsTo(Block::class);
    }

    public function fabrications(): HasMany
    {
        return $this->hasMany(PieceFabrication::class);
    }

}
