<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;

class ApiAuthenticator extends AbstractAuthenticator
{
    public function __construct(
        private readonly string $apiHeaderName,
        private readonly string $apiKey
    ) {}

    public function supports(Request $request): ?bool
    {
        return true; // Applique l'auth sur toutes les routes (vu que microservice)
    }

    public function authenticate(Request $request): SelfValidatingPassport
    {
        if (!$request->headers->has($this->apiHeaderName)) {
            throw new AuthenticationException('Mailer API Key is missing.');
        }

        $headerValue = $request->headers->get($this->apiHeaderName);

        if ($headerValue !== $this->apiKey) {
            throw new AuthenticationException('Invalid Mailer API Key.');
        }

        return new SelfValidatingPassport(new UserBadge('api_user'));
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?JsonResponse
    {
        return null; // Rien à faire si tout va bien
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): JsonResponse
    {
        return new JsonResponse(['message' => 'Unauthorized: ' . $exception->getMessage()], JsonResponse::HTTP_UNAUTHORIZED);
    }
}
