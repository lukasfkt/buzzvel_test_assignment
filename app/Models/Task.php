<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    # Attributes to be filled
    protected $fillable = [
        'title',
        'description',
        'file_location',
        'completed',
        'user_id'
    ];

    # Relationship with the Schedule class
    public function user()
    {
        return $this->hasOne(User::class);
    }
}
