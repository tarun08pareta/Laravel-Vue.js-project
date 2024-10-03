<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Condition extends Model
{
    use HasFactory;
    protected $fillable = ['name'];


    public function products(): HasMany
    {
        return $this->hasMany(Product::class, );
    }

    // public function getAllProducts(): Collection
    // {
    //     return $this->products()->get();
    // }
}
