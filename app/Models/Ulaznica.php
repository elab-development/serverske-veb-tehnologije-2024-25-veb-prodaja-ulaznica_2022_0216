<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ulaznica extends Model
{
    protected $table = 'ulaznice';

    protected $fillable = [
        'dogadjaj_id', 'kupovina_id', 'kod', 'tip', 'sediste', 'cena', 'status',
    ];

    protected $casts = [
        'cena' => 'decimal:2',
    ];

    /** Događaj za koji je ulaznica */
    public function dogadjaj(): BelongsTo
    {
        return $this->belongsTo(Dogadjaj::class, 'dogadjaj_id');
    }

    /** Kupovina u okviru koje je ulaznica prodata (može biti null dok je “dostupna”) */
    public function kupovina(): BelongsTo
    {
        return $this->belongsTo(Kupovina::class, 'kupovina_id');
    }
}
