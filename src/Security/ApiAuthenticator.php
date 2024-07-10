<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class ApiAuthenticator extends AbstractAuthenticator
{
    public function supports(Request $request): ?bool
    {
        //supporte l'authentification s'il y a une en-tête "Authorization" associée au mot-clé "Bearer"
        return $request->headers->has("Authorization") && str_contains($request->headers->get("Authorization"), "Bearer");
    }


    public function authenticate(Request $request): Passport
    {
        //extraire l'identifieur de la requête = la clé d'API
        $identifier = str_replace("Bearer", "", $request->headers->get("Authorization"));

        //le User provider récupérera le User à partir de cet identifieur
        return new SelfValidatingPassport(new UserBadge($identifier));
    }


    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        //je laisse le système continuer
        return null;
    }


    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return new JsonResponse(["message" => $exception->getMessage()], Response::HTTP_UNAUTHORIZED);
    }
}