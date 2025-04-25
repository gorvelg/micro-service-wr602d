<?php

namespace App\Controller;

use App\Service\MailerService;
use App\Service\Utils\RequestChecker;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class MailerController extends AbstractController
{
    public function __construct(
        private readonly MailerService $mailerService,
        private readonly RequestChecker $requestChecker,
    ) {}

    #[Route('/send-mail', name: 'send_mail', methods: ['POST'])]
    public function index(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Vérifie les champs obligatoires
        $checkFieldsResponse = $this->requestChecker->checkRequiredFields($data, ['to', 'subject', 'message']);
        if ($checkFieldsResponse) {
            return $checkFieldsResponse;
        }

        $email = $data['to'];
        $subject = $data['subject'];
        $message = $data['message'];

        // Vérifie l'email
        $checkEmailResponse = $this->requestChecker->checkEmail($email);
        if ($checkEmailResponse) {
            return $checkEmailResponse;
        }

        // Envoie de l'email
        $this->mailerService->sendEmail($email, $subject, $message);

        return new JsonResponse(['message' => 'Mail sent successfully!', 'status' => 200]);
    }
}
