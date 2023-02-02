<?php

namespace App\Services;

use App\Entity\User;
use App\Repository\UserRepository;

class UserService
{
    public function __construct(private UserRepository $userRepository)
    {
        
    }

    /**
     * Store user in database
     *
     * @param User $user
     * @return void
     */
    public function saveUser(User $user) : void
    {
        if($user instanceof User)
        {
            $this->userRepository->save($user, true);
        }
    }

    /**
     * find User by target property
     *
     * @param String $target
     * @param String $value
     * @return User|null
     */
    public function findUser(String $target, String $value) : User|null
    {
        return $this->userRepository->findOneBy([$target => $value]);
    }

    /**
     * Remove user
     *
     * @param User $user
     * @return void
     */
    public function removeUser(User $user) : void
    {
        if($user instanceof User)
        {
            $this->userRepository->remove($user, true);
        }
    }

    /**
     * Find User and delete
     *
     * @param String $target
     * @param String $value
     * @return void
     */
    public function findOneAndDelete(String $target, String $value)
    {
        $user = $this->userRepository->findOneBy([$target => $value]);

        return $this->removeUser($user);
    }
}