<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PieceFabrication extends Model
{

    protected $table = 'piece_fabrications';

    protected $fillable = [
        'piece_id', 'manufactured_at', 'real_weight', 
        'weight_diff', 'status', 'observations', 'created_by'
    ];

    public function piece(): BelongsTo
    {

        return $this->belongsTo(Piece::class);
        
    }

    protected static function booted()
    {

        static::creating(function ($fabrication) {
            $fabrication->weight_diff = $fabrication->calculateDiff();
        });

        static::updating(function ($fabrication) {
            $fabrication->weight_diff = $fabrication->calculateDiff();
        });

    }

    public function calculateDiff()
    {
        
        $piece = \App\Models\Piece::find($this->piece_id);
        
        if (!$piece) {
            return 0; 
        }

        return $this->real_weight - $piece->theoretical_weight;

    }

}

