<?php

namespace App\Repositories\Interfaces;

use App\Models\Location;

interface LocationRepositoryInterface
{
    public function create(array $data): Location;

    public function update(Location $location, array $data): Location;

    public function delete(Location $location): void;

    public function getById($id): Location;

}
