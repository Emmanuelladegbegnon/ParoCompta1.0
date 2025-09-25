<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = [
        'parish_id',
        'name',
        'template_file',
        'fields_json',
    ];

    protected $casts = [
        'fields_json' => 'array',
    ];

    public function parish(): BelongsTo
    {
        return $this->belongsTo(Parish::class);
    }

    public function entries(): HasMany
    {
        return $this->hasMany(Entry::class);
    }
}
