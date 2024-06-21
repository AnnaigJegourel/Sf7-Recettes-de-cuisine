<?php

namespace App\Security;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\SecurityRequestAttributes;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class AppAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;    //cf. ci-dessous onAuthenticationSuccess

    public const LOGIN_ROUTE = 'app_login';

    public function __construct(
        private UrlGeneratorInterface $urlGenerator, 
        private UserRepository $userRepository)
    {
    }

    //fonction héritée de AbstractLoginFormAuthenticator
    public function authenticate(Request $request): Passport
    {
        $username = $request->getPayload()->getString('username');

        $request->getSession()->set(SecurityRequestAttributes::LAST_USERNAME, $username);

        //Passport est un objet du composant Security qui détermine si onAuthenticationSuccess() ou onAuthenticationFailure()
        return new Passport(
            //customisation pour prendre aussi l'email comme identifiant
            new UserBadge($username, fn(string $identifier) => $this->userRepository->findUserByEmailOrUsername($identifier)),
            //version fonction non fléchée
            //new UserBadge($username, function(string $identifier){...}),

            //vérifie mot de passe haché (bdd) ; vérifie csrf token ; rememberMe (crée cookie)
            new PasswordCredentials($request->getPayload()->getString('password')),
            [
                new CsrfTokenBadge('authenticate', $request->getPayload()->getString('_csrf_token')),
                new RememberMeBadge(),
            ]
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        //Par défaut, redirige l’U vers la page cherchée au début (géré par TargetPathTrait)
        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }

        //on peut ajouter sa propre redirection...
        //...en utilisant UrlGeneratorInterface (injecté dans constructeur ci-dessus)
        //return new RedirectResponse($this->urlGenerator->generate('admin.recipe.index'));
        //... ou sans
        return new RedirectResponse('/');

        //throw new \Exception('TODO: provide a valid redirect inside '.__FILE__);
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}
