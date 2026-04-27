<?php

namespace App\QueryFilters;

use Illuminate\Http\Request;

class TicketFilter
{
    public function apply($query, array $filters)
    {
        return $query
            ->when(!empty($filters['status']), fn ($q) =>
                $q->where('status', $filters['status'])
            )
            ->when(!empty($filters['priority']), fn ($q) =>
                $q->where('priority', $filters['priority'])
            )
            ->when(!empty($filters['category_id']), fn ($q) =>
                $q->where('category_id', $filters['category_id'])
            )
            ->when(!empty($filters['assigned_to']), fn ($q) =>
                $q->where('assigned_to', $filters['assigned_to'])
            )
            ->when(!empty($filters['created_from']), fn ($q) =>
                $q->whereDate('created_at', '>=', $filters['created_from'])
            )
            ->when(!empty($filters['created_to']), fn ($q) =>
                $q->whereDate('created_at', '<=', $filters['created_to'])
            )
            ->when(!empty($filters['search']), fn ($q) =>
                $q->where('title', 'like', "%{$filters['search']}%")
            )
            ->when(isset($filters['reopened']), function ($q) use ($filters) {
                if ($filters['reopened']) {
                    $q->where('reopened', true);
                }
            });
    }
}
