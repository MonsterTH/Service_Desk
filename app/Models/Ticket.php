<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'status',
        'priority',
        'created_by',
        'assigned_to',
        'category_id',
    ];

    protected $with = ['category', 'creator', 'assignee'];

    /*
    |-----------------------------
    | RELATIONSHIPS
    |-----------------------------
    */

    // quem criou o ticket
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // técnico atribuído
    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    // categoria do ticket
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // comentários do ticket
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
