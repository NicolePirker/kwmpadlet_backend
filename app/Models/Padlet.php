<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Padlet extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'name'];

    // Ein Padlet gehört zu einem User (Autor)
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Ein Padlet kann mehrere Einträge haben
    public function entries(): HasMany
    {
        return $this->hasMany(Entry::class);
    }

    // Ein Padlet kann mehreren Usern zugeordnet sein (wenn Padlet geteilt wird)
    // Diese Funktion bezieht sich auf die User mit denen Padlets geteilt wurden
    public function sharedWith(): BelongsToMany
    {
        // pivot: https://laravel.com/docs/5.0/eloquent#working-with-pivot-tables
        return $this->belongsToMany(User::class, 'user_padlet')->withPivot('role');
    }
}
