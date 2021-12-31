<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Word extends Model
{
    use HasFactory;

    protected $fillable = [
        'word_foreign',
        'word_translation',
        'learning_status',
        'times_reviewed',
        'user_id',
        'dictionary_id',
        'is_turned',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function dictionary()
    {
        return $this->belongsTo(Dictionary::class);
    }
}
