<?php

namespace App\Security;

use App\Entity\Article;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class PostVoter extends Voter
{
    public const SHOW = 'show';
    public const EDIT = 'edit';
    public const DELETE = 'delete';
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports($attribute, $subject)
    {
        if (!\in_array($attribute, [self::SHOW, self::EDIT, self::DELETE], true)) {
            return false;
        }
        // only vote on Article objects inside this voter
        if (!$subject instanceof Article) {
            return false;
        }

        return true;
    }

    /**
     * @param string $attribute
     * @param mixed $subject
     * @param TokenInterface $token
     *
     * @return bool
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }
        // ROLE_SUPER_ADMIN can edit all articles
        if ($this->security->isGranted('ROLE_SUPER_ADMIN')) {
            return true;
        }

        $article = $subject;

        switch ($attribute) {
            case self::SHOW:
                return $this->canAction($article, $user);
            case self::EDIT:
                return $this->canAction($article, $user);
            case self::DELETE:
                return $this->canAction($article, $user);
        }

        throw new \LogicException('This code should not be reached!');
    }

    private function canAction(Article $article, User $user): bool
    {
        return $user === $article->getAuthor();
    }
}
