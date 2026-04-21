<?php

namespace App\QueryFilters;

use Illuminate\Http\Request;

class TicketFilter
{
    public function apply($query, Request $request)
    {
        return $query
            ->when($request->filled('status'), fn ($q) =>
                $q->where('status', $request->status)
            )
            ->when($request->filled('priority'), fn ($q) =>
                $q->where('priority', $request->priority)
            )
            ->when($request->filled('category_id'), fn ($q) =>
                $q->where('category_id', $request->category_id)
            )
            ->when($request->filled('assigned_to'), fn ($q) =>
                $q->where('assigned_to', $request->assigned_to)
            )
            ->when($request->filled('created_from'), fn ($q) =>
                $q->whereDate('created_at', '>=', $request->created_from)
            )
            ->when($request->filled('created_to'), fn ($q) =>
                $q->whereDate('created_at', '<=', $request->created_to)
            )
            ->when($request->filled('search'), fn ($q) =>
                $q->where('title', 'like', "%{$request->search}%")
            )
            ->when($request->filled('reopened'), function ($q) use ($request) {
                if ($request->boolean('reopened')) {
                    $q->where('reopened', true);
                }
            });
            // ->when($request->has('reopened') && $request->input('reopened') == 'true', function ($query) {
            //     $query->where('status', 'open')
            //         ->whereHas('logs', function ($q) {
            //             $q->where('action', 'status_changed')
            //             ->where('changes->from', 'resolved')
            //             ->where('changes->to', 'open');
            //         });
            // });
    }
}
