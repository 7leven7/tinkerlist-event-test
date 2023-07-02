<?php

namespace App\Repositories\Interfaces;

use App\Models\User;

interface UserRepositoryInterface
{
    public function create(array $data): User;

    public function findByEmail(string $email): User;

    public function update(User $user, array $data): User;

    public function delete(User $user): void;

    public function getById($id): User;
}
