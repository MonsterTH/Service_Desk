<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'title'       => $this->title,
            'description' => $this->description,
            'status'      => $this->status,
            'priority'    => $this->priority,
            'category_id' => $this->category_id,
            'assigned_to' => $this->assigned_to,
            'created_by'  => $this->created_by,
            'created_at'  => $this->created_at->diffForHumans(),
            'updated_at'  => $this->updated_at->diffForHumans(),

            'category' => $this->whenLoaded('category', fn() => [
                'id'   => $this->category->id,
                'name' => $this->category->name,
            ]),

            'creator' => $this->whenLoaded('creator', fn() => [
                'id'   => $this->creator->id,
                'name' => $this->creator->name,
            ]),

            'assignee' => $this->whenLoaded('assignee', fn() => [
                'id'   => $this->assignee->id,
                'name' => $this->assignee->name,
            ]),

            'deleted_at' => $this->deleted_at ? $this->deleted_at->diffForHumans() : null,
        ];
    }
}
