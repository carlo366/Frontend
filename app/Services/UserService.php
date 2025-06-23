<?php

namespace App\Services;

use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Http;

class UserService
{
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }


    public function getAllUsers()
    {
        return $this->userRepository->all();
    }


    public function count(){
        return $this->userRepository->count();
    }

    public function getUserById($id)
    {
        return $this->userRepository->find($id);
    }

    public function createUser(array $data)
    {
        return $this->userRepository->create($data);
    }

    public function updateUser(array $data, $id)
    {
        return $this->userRepository->update($data, $id);
    }
    public function deleteUser($id)
    {
        return $this->userRepository->delete($id);
    }
    public function changePassword(array $data, $token)
{
    return Http::withToken($token)
        ->post(config('api.base_url') . '/users/change-password', $data);
}
}
