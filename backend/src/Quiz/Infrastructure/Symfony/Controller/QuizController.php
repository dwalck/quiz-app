<?php

declare(strict_types=1);

namespace App\Quiz\Infrastructure\Symfony\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
final class QuizController
{
    #[Route('/quiz')]
    public function __invoke(): JsonResponse
    {
        return new JsonResponse([]);
    }
}
