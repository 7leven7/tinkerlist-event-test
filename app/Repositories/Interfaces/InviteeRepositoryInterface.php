<?php

namespace App\Repositories\Interfaces;

use App\Models\Invitee;

interface InviteeRepositoryInterface
{
    /**
     * @param array $data
     * @return Invitee
     */
    public function create(array $data): Invitee;

    /**
     * @param Invitee $invitee
     * @param array $data
     * @return Invitee
     */
    public function update(Invitee $invitee, array $data): Invitee;

    /**
     * @param Invitee $invitee
     * @return void
     */
    public function delete(Invitee $invitee): void;

    /**
     * @param $id
     * @return Invitee
     */
    public function getById($id): Invitee;
}
