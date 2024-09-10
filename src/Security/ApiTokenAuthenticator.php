<?php

namespace App\Security;

use App\Repository\ApiTokenRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;

class ApiTokenAuthenticator extends AbstractGuardAuthenticator
{

    private ApiTokenRepository $apiTokenRepository;

    public function __construct(ApiTokenRepository $apiTokenRepository)
    {
        $this->apiTokenRepository = $apiTokenRepository;
    }

    public function start(Request $request, AuthenticationException $authException = null): Response
    {
        throw new \Exception('Never called!');
    }

    public function supports(Request $request): bool
    {
        return $request->headers->has('Authorization') && str_starts_with($request->headers->get('Authorization'), 'Bearer');
    }

    public function getCredentials(Request $request)
    {
        return substr($request->headers->get('Authorization'), 7);
    }

    public function getUser($credentials, UserProviderInterface $userProvider): ?UserInterface
    {
        $token = $this->apiTokenRepository->findOneBy(['token' => $credentials]);

        if (! $token) {
            throw new CustomUserMessageAuthenticationException('Invalid token');
        }

        if ($token->isExpired()) {
            throw new CustomUserMessageAuthenticationException('Token expired');
        }

        return $token->getUser();
    }

    public function checkCredentials($credentials, UserInterface $user): bool
    {
        return true;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        return new JsonResponse([
            'message' => $exception->getMessage(),
        ], 401);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $providerKey)
    {
        // continue
    }

    public function supportsRememberMe(): bool
    {
        return false;
    }

}
