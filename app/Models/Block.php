<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Block extends Model
{

    use SoftDeletes;

    protected $fillable = [
        'code', 'project_id', 'name', 'description', 'status', 'created_by'
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function pieces(): HasMany
    {
        return $this->hasMany(Piece::class);
    }

}
