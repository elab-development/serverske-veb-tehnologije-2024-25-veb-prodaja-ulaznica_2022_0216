<?php

namespace app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use app\Models\Dogadjaj;

class Mesto extends Model
{
    protected $table = 'mesta';

    protected $fillable = [
        'naziv', 'adresa', 'grad', 'kapacitet',
    ];

    /** Događaji koji se održavaju na ovom mestu */
    public function dogadjaji(): HasMany
    {
        return $this->hasMany(Dogadjaj::class, 'mesto_id');
    }
}
