<?php

namespace App\Security\Voter;

use App\Entity\Recipe;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;


class RecipeVoter extends Voter
{
    public const EDIT = 'RECIPE_EDIT';
    public const VIEW = 'RECIPE_VIEW';


    /**
     * dit si on supporte ou non cette permission
     *
     * @param string $attribute
     * @param mixed $subject
     * @return boolean
     */
    protected function supports(string $attribute, mixed $subject): bool
    {
        // code par défaut
        // https://symfony.com/doc/current/security/voters.html
        //si on a une des permissions définies ci-dessus ET qu'on vérifie la permission sur une instance de Recipe, renvoie true
        return in_array($attribute, [self::EDIT, self::VIEW])
            && $subject instanceof \App\Entity\Recipe;
    }


    /**
     * faire le vote
     *
     * @param string $attribute nom de la permission
     * @param mixed $subject la recette
     * @param TokenInterface $token au niveau de la sécurité, on y récupère l'user
     * @return boolean
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        // if the user is anonymous, do not grant access
        if (!$user instanceof User) {
            return false;
        }

        //si le sujet n'est pas de type Recipe (ne devrait pas arriver car condition ligne 29)
        if (!$subject instanceof Recipe) {
            return false;
        }

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::EDIT:
                // logic to determine if the user can EDIT
                // return true or false
                //condition: si l'utilisateur auteur de la recette est l'utilisateur connecté
                return $subject->getAuthor()->getId() === $user->getId();
                break;

            case self::VIEW:
                // logic to determine if the user can VIEW
                // return true or false
                break;
        }

        return false;
    }
}
