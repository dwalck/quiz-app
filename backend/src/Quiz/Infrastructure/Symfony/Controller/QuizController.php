<?php

namespace App\Quiz\Infrastructure\Symfony\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
final class QuizController
{
    public function __invoke(): JsonResponse
    {



    }
}
