<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Dogadjaj extends Model
{
    protected $table = 'dogadjaji';

    protected $fillable = [
        'naziv', 'opis', 'mesto_id', 'datum_pocetka', 'datum_zavrsetka', 'kategorija',
    ];

    protected $casts = [
        'datum_pocetka'   => 'datetime',
        'datum_zavrsetka' => 'datetime',
    ];

    /** Mesto na kojem se održava događaj */
    public function mesto(): BelongsTo
    {
        return $this->belongsTo(Mesto::class, 'mesto_id');
    }

    /** Sve ulaznice za ovaj događaj */
    public function ulaznice(): HasMany
    {
        return $this->hasMany(Ulaznica::class, 'dogadjaj_id');
    }
}
