<?php

namespace App\Helpers;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class PaginationHelper
{
    /**
     * @param Collection $items
     * @param int $perPage
     * @param int $currentPage
     * @return LengthAwarePaginator
     */
    public function paginate(Collection $items, int $perPage, int $currentPage): LengthAwarePaginator
    {
        $total = $items->count();

        $paginatedItems = $items->slice(($currentPage - 1) * $perPage, $perPage)->values();

        return new LengthAwarePaginator(
            $paginatedItems,
            $total,
            $perPage,
            $currentPage,
            ['path' => LengthAwarePaginator::resolveCurrentPath()]
        );
    }

}
