<?php

namespace App\Repositories\Interfaces;

use App\Models\User;

interface UserRepositoryInterface
{
    /**
     * @param array $data
     * @return User
     */
    public function create(array $data): User;

    /**
     * @param User $user
     * @param array $data
     * @return User
     */
    public function update(User $user, array $data): User;

    /**
     * @param User $user
     * @return void
     */
    public function delete(User $user): void;

    /**
     * @param $id
     * @return User
     */
    public function getById($id): User;
}
