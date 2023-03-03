<?php

namespace App\Security\Voter;

use App\Entity\Personasjuridicas;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class PersonaJuridicaVoter extends Voter
{
    const VIEW = 'ver';
    const EDIT = 'editar';
    protected function supports($attribute, $subject)
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, ['ver', 'editar'])
            && $subject instanceof \App\Entity\Personasjuridicas;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        $pj = $subject;

        switch ($attribute) {
            case 'ver':
                return true;/*$this->canView($pj, $user);*/
                break;
            case 'editar':
                return true;
//                    $this->canEdit($pj, $user);
                break;
        }

        return false;
    }
    private function canView(Personasjuridicas $pj, User $user)
    {
        // if they can edit, they can view
//        if ($this->canEdit($pj, $user)) {
//            return true;
//        }

        // the Post object could have, for example, a method `isPrivate()`
        return true;
    }

    private function canEdit(Personasjuridicas $pj, User $user)
    {
        return true;
    }
}
