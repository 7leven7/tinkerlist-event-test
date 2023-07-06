<?php

namespace App\Repositories\Interfaces;

use App\Models\Location;

interface LocationRepositoryInterface
{
    /**
     * @param array $data
     * @return Location
     */
    public function create(array $data): Location;

    /**
     * @param Location $location
     * @param array $data
     * @return Location
     */
    public function update(Location $location, array $data): Location;

    /**
     * @param Location $location
     * @return void
     */
    public function delete(Location $location): void;

    /**
     * @param $id
     * @return Location
     */
    public function getById($id): Location;

}
