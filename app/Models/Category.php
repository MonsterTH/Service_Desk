<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'is_active',
    ];

    /*
    |-----------------------------
    | RELATIONSHIPS
    |-----------------------------
    */

    // uma categoria tem muitos tickets
    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    /*
    |-----------------------------
    | SCOPES (opcional mas útil)
    |-----------------------------
    */

    // apenas categorias ativas
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
