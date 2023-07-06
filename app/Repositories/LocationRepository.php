<?php

namespace App\Repositories;

use App\Models\Location;
use App\Repositories\Interfaces\LocationRepositoryInterface;
use Exception;
use Illuminate\Support\Facades\Validator;

class LocationRepository implements LocationRepositoryInterface
{
    /**
     * @param array $data
     * @return Location
     * @throws Exception
     */
    public function create(array $data): Location
    {
        try {
            $validator = Validator::make($data, [
                'name' => 'required|string',
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            return Location::create($data);
        } catch (Exception $e) {
            throw new Exception('Failed to create location.', 500, $e);
        }
    }

    /**
     * @param Location $location
     * @param array $data
     * @return Location
     * @throws Exception
     */
    public function update(Location $location, array $data): Location
    {
        try {
            $validator = Validator::make($data, [
                'name' => 'required|string',
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $location->update($data);

            return $location;
        } catch (Exception $e) {
            throw new Exception('Failed to update location.', 500, $e);
        }
    }

    /**
     * @param Location $location
     * @return void
     * @throws Exception
     */
    public function delete(Location $location): void
    {
        try {
            $location->delete();
        } catch (Exception $e) {
            throw new Exception('Failed to delete location.', 500, $e);
        }
    }

    /**
     * @param $id
     * @return Location
     * @throws Exception
     */
    public function getById($id): Location
    {
        try {
            return Location::findOrFail($id);
        } catch (Exception $e) {
            throw new Exception('Failed to find location by id.', 500, $e);

        }
    }

}
