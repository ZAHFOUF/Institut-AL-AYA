<?php

namespace AlAya\Agent\SecurityBundle\Security;

use AlAya\Common\Entity\Agent;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class adminChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user): void {
        if (!$user instanceof Agent) {
            return;
        }

        if ($user->getDeleted()) {
            // the message passed to this exception is meant to be displayed to the user
            throw new CustomUserMessageAccountStatusException('Your user account no longer exists.');
        }

        if (!$user->getEnabled()) {
            // the message passed to this exception is meant to be displayed to the user
            throw new CustomUserMessageAccountStatusException('Your user account is not active.');
        }
    }

    public function checkPostAuth(UserInterface $user): void
    {
        if (!$user instanceof Agent) {
            return;
        }
    }
}