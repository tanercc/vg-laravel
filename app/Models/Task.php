<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'time_estimate',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    #[Scope]
    protected function scopeForUser($query, $user)
    {
        return $query->where('user_id', $user->id);
    }
}
