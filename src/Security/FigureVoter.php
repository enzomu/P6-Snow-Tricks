<?php

namespace App\Security;

use App\Entity\Figure;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class FigureVoter extends Voter
{
    const CREATE = 'FIGURE_CREATE';
    const EDIT = 'FIGURE_EDIT';
    const DELETE = 'FIGURE_DELETE';

    protected function supports(string $attribute, $subject): bool
    {
        if ($attribute === self::CREATE) {
            return true;
        }

        return in_array($attribute, [self::EDIT, self::DELETE])
            && $subject instanceof Figure;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        return $user instanceof User && $user->isVerified();
    }
}