<?php

namespace App\Repositories;

use App\Models\User;
use Exception;
use Illuminate\Validation\ValidationException;
use App\Repositories\Interfaces\UserRepositoryInterface;

class UserRepository implements UserRepositoryInterface
{
    /**
     * @param array $data
     * @return User
     * @throws Exception
     */
    public function create(array $data): User
    {
        try {
            $validator = \Validator::make($data, [
                'name' => 'required|string',
                'email' => 'required|string|email|unique:users',
                'password' => 'required|string|min:6',
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            return User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => bcrypt($data['password']),
            ]);
        } catch (Exception $e) {
            throw new Exception('Failed to create user.', 500, $e);
        }
    }

    /**
     * @param User $user
     * @param array $data
     * @return User
     * @throws Exception
     */
    public function update(User $user, array $data): User
    {
        try {
            $validator = Validator::make($data, [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $user->id,
                'password' => 'nullable|string|min:6',
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $user->update($data);

            return $user;
        } catch (Exception $e) {
            throw new Exception('Failed to update user.', 500, $e);

        }
    }

    /**
     * @param User $user
     * @return void
     * @throws Exception
     */
    public function delete(User $user): void
    {
        try {
            $user->delete();
        } catch (Exception $e) {
            throw new Exception('Failed to delete user.', 500, $e);
        }
    }

    /**
     * @param $id
     * @return User
     * @throws Exception
     */
    public function getById($id): User
    {
        try {
            return User::findOrFail($id);
        } catch (Exception $e) {
            throw new Exception('Failed to find user by id.', 500, $e);

        }
    }
}
