<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id',
        'user_id',
        'comment',
        'is_internal',
    ];

    /*
    |-----------------------------
    | RELATIONSHIPS
    |-----------------------------
    */

    // comentário pertence a um ticket
    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    // comentário pertence a um utilizador
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
