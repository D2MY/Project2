<?php

namespace App\Service;

use App\Entity\Chapter;
use App\Entity\User;
use App\Repository\CompositionRepository;
use Doctrine\ORM\EntityManagerInterface;

class AdminService
{
    public function changeRole(User $user) :User
    {
        in_array('ROLE_ADMIN', $user->getRoles(), true)?$user->setRoles(['ROLE_USER']):$user->setRoles(['ROLE_ADMIN']);
        return $user;
    }
}
