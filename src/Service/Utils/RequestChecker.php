<?php

namespace App\Service\Utils;

use Symfony\Component\HttpFoundation\JsonResponse;

class RequestChecker
{
    /**
     * Vérifie si l'email est valide.
     */
    public function checkEmail(string $email): ?JsonResponse
    {
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return new JsonResponse(['message' => 'Invalid email format.', 'status' => 400]);
        }

        return null;
    }

    /**
     * Vérifie que tous les champs attendus sont présents.
     */
    public function checkRequiredFields(array $data, array $requiredFields): ?JsonResponse
    {
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                return new JsonResponse(['message' => "Missing field: $field", 'status' => 400]);
            }
        }

        return null;
    }
}
