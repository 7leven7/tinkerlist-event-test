<?php

namespace App\Repositories\Interfaces;

use App\Models\Event;
use Illuminate\Database\Eloquent\Collection;

interface EventRepositoryInterface
{
    public function create(array $data): Event;

    public function update(Event $event, array $data): Event;

    public function delete(Event $event): void;

    public function getById($id): Event;

    public function getByDateRange($data): Collection;
    
}
