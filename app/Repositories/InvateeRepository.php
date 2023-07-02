<?php

namespace App\Repositories;

use App\Models\Invitee;
use App\Repositories\Interfaces\InviteeRepositoryInterface;
use Illuminate\Support\Facades\Validator;

class InviteeRepository implements InviteeRepositoryInterface
{
    public function create(array $data): Invitee
    {
        try {
            $validator = Validator::make($data, [
                'name' => 'required|string',
                'email' => 'required|string|email|unique:invitees',
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            return Invitee::create([
                'name' => $data['name'],
                'email' => $data['email'],
            ]);
        } catch (\Exception $e) {
            throw new \Exception('Failed to create invatee.', 500, $e);
        }
    }

    public function update(Invitee $invitee, array $data): Invitee
    {
        try {
            $validator = Validator::make($data, [
                'name' => 'required|string',
                'email' => 'required|string|email|unique:invitees,email,' . $invitee->id,
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $invitee->update($data);
          
            return $invitee;

        } catch (\Exception $e) {
            throw new \Exception('Failed to update invatee.', 500, $e);
        }
    }

    public function delete(Invitee $invitee): void
    {
        try {
            $invitee->delete();
        } catch (\Exception $e) {
            throw new \Exception('Failed to delete invatee.', 500, $e);
        }
    }

    public function getById($id): Invitee
    {
        try {
            $invitee = Invitee::findOrFail($id);
            return $invitee;
        } catch (\Exception $e) {
            throw new \Exception('Failed to find invatee by id.', 500, $e);
        }
    }

}
