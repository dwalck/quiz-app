<?php

declare(strict_types=1);

namespace App\Quiz\Infrastructure\Symfony\Controller;

use App\Quiz\Application\Command\StartQuiz\StartQuizCommand;
use App\Quiz\Domain\ValueObject\QuizId;
use App\SharedKernel\Application\CommandDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
final readonly class StartQuizController
{
    public function __construct(
        private CommandDispatcherInterface $commandDispatcher,
    ) {
    }

    #[Route('/quiz/{id}/start', name: 'quiz_start', methods: 'POST')]
    public function __invoke(string $id): JsonResponse
    {
        $this->commandDispatcher->dispatch(new StartQuizCommand(
            QuizId::fromString($id),
        ));

        return new JsonResponse((object) []);
    }
}
