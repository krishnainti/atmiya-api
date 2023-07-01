<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\HasMany;


class ChapterState extends Model
{
    use HasFactory;

    public function metroAreas(): HasMany
    {
        return $this->hasMany(MetroAreas::class);
    }
}
