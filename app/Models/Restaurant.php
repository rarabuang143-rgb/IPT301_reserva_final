<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Restaurant extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'cuisine', 'phone', 'address'];

    public function foods(): HasMany
    {
        return $this->hasMany(Food::class);
    }

    public function orders(): HasManyThrough
    {
        return $this->hasManyThrough(Order::class, Food::class);
    }
}
