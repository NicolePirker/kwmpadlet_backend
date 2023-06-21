<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Comment extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'text', 'entry_id'];

    // Ein Kommentar gehört zu einem User
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Ein Kommentar gehört zu einem Entry
    public function entry(): BelongsTo
    {
        return $this->belongsTo(Entry::class);
    }
}
