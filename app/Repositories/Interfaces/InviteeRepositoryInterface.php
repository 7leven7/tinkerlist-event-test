<?php

namespace App\Repositories\Interfaces;

use App\Models\Invitee;

interface InviteeRepositoryInterface
{
    public function create(array $data): Invitee;

    public function update(Invitee $invitee, array $data): Invitee;

    public function delete(Invitee $invitee): void;

    public function getById($id): Invitee;
}
