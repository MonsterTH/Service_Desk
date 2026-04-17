<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Observers\TicketObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;

#[ObservedBy(TicketObserver::class)]
class Ticket extends Model
{
    use HasFactory, SoftDeletes;

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

    public function isFinalState(): bool
    {
        return in_array($this->status, ['resolved', 'closed']);
    }

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

    public function logs()
    {
        return $this->hasMany(TicketLog::class)->latest();
    }
}
