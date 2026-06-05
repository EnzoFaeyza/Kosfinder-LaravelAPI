<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Review extends Model
{
    protected $fillable = [
        'kost_id',
        'user_id',
        'rating',
        'komentar',
    ];
    public function user()
{
    return $this->belongsTo(User::class);
}
}