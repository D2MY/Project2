<?php

namespace App\Service;

use Symfony\Component\Security\Core\Authentication\Token\SwitchUserToken;
use Symfony\Component\Security\Csrf\TokenStorage\TokenStorageInterface;

class Security
{
    public function isSwitch(TokenStorageInterface $tokenStorage, $token) :int
    {
        if ($token instanceof SwitchUserToken) {
            $isSwitch = 1;
        } else {
            $isSwitch = 0;
        }
        return $isSwitch;
    }
}
