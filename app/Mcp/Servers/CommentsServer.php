<?php

namespace App\Mcp\Servers;

use App\Mcp\Resources\GetAllComments;
use App\Mcp\Resources\GetComment;
use App\Mcp\Tools\CreateComment;
use App\Mcp\Tools\DeleteComment;
use App\Mcp\Tools\UpdateComment;
use Laravel\Mcp\Server;
use Laravel\Mcp\Server\Attributes\Instructions;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Attributes\Version;

#[Name('CommentsServer')]
#[Version('1.0.0')]
#[Instructions(
        'This server allows managing the comments on a ticket.
        Use CreateComment to create comments.
        Use DeleteComment to soft delete (only when explicitly requested) a comment.
        Always confirm before deleting a comment.
        Use UpdateComment to update a comment.
    ')]
class CommentsServer extends Server
{
    protected array $tools = [
        CreateComment::class,
        DeleteComment::class,
        UpdateComment::class,
    ];

    protected array $resources = [
        GetAllComments::class,
        GetComment::class,
    ];

    protected array $prompts = [
        //
    ];
}
