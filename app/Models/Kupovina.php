<?php

namespace app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Kupovina extends Model
{
    protected $table = 'kupovine';

    protected $fillable = [
        'broj_porudzbine', 'korisnik_id', 'ukupno', 'valuta', 'nacin_placanja', 'stanje',
    ];

    protected $casts = [
        'ukupno' => 'decimal:2',
    ];

    /** Korisnik kome pripada kupovina (može biti null za “guest checkout”) */
    public function korisnik(): BelongsTo
    {
        return $this->belongsTo(User::class, 'korisnik_id');
    }

    /** Ulaznice kupljene u ovoj kupovini */
    public function ulaznice(): HasMany
    {
        return $this->hasMany(Ulaznica::class, 'kupovina_id');
    }
}
