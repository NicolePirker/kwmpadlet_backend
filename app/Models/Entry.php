<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Entry extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'text', 'padlet_id'];

    // Ein Entry gehört zu einem User
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Ein Entry gehört zu einem Padlet
    public function padlet(): BelongsTo
    {
        return $this->belongsTo(Padlet::class);
    }

    // Ein Entry kann mehrere Kommentare haben
    public function comments(): HasMany{
        return $this->hasMany(Comment::class);
    }

    // Ein Entry kann mehrere Ratings haben
    public function ratings(): HasMany{
        return $this->hasMany(Rating::class);
    }
}
