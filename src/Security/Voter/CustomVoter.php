<?php

namespace App\Security\Voter;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class CustomVoter extends Voter
{
    public const EDIT = 'edit';
    public const DELETE = 'delete';
    public const CHAPTER_CREATE = 'chapter create';
    public const CHAPTER_EDIT = 'chapter edit';
    public const CHAPTER_DELETE = 'chapter delete';

    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, [self::DELETE, self::EDIT, self::CHAPTER_CREATE, self::CHAPTER_EDIT, self::CHAPTER_DELETE], true)
            && $subject instanceof User;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false;
        }

        switch ($attribute) {
            case self::EDIT:
                return $this->canEdit($subject, $user);
            case self::DELETE:
                return $this->canDelete($subject, $user);
            case self::CHAPTER_CREATE:
                return $this->canChapterCreate($subject, $user);
            case self::CHAPTER_EDIT:
                return $this->canChapterEdit($subject, $user);
            case self::CHAPTER_DELETE:
                return $this->canChapterDelete($subject, $user);
        }

        return false;
    }

    private function canEdit($subject, $user): bool
    {
        return $subject === $user;
    }

    private function canDelete($subject, $user): bool
    {
        return $subject === $user;
    }

    private function canChapterCreate($subject, $user): bool
    {
        return $subject === $user;
    }

    private function canChapterEdit($subject, $user): bool
    {
        return $subject === $user;
    }

    private function canChapterDelete($subject, $user): bool
    {
        return $subject === $user;
    }
}
