<?php

namespace App\Repositories\Interfaces;

use App\Models\Event;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface EventRepositoryInterface
{
    /**
     * @param array $data
     * @return Event
     */
    public function create(array $data): Event;

    /**
     * @param Event $event
     * @param array $data
     * @return Event
     */
    public function update(Event $event, array $data): Event;

    /**
     * @param Event $event
     * @return void
     */
    public function delete(Event $event): void;

    /**
     * @param $id
     * @return Event
     */
    public function getById($id): Event;

    /**
     * @param $data
     * @return LengthAwarePaginator
     */
    public function getByDateRange($data): LengthAwarePaginator;

    /**
     * @param array $data
     * @return Collection
     */
    public function getLocationsByDateInterval(array $data): Collection;


}
