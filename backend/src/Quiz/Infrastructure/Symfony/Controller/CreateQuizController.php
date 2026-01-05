<?php

declare(strict_types=1);

namespace App\Quiz\Infrastructure\Symfony\Controller;

use App\Quiz\Application\Command\CreateQuiz\CreateQuizCommand;
use App\Quiz\Domain\ValueObject\QuizId;
use App\Quiz\Infrastructure\Symfony\Model\CreateQuiz\Request\CreateQuizRequestModel;
use App\SharedKernel\Application\CommandDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
final readonly class CreateQuizController
{
    public function __construct(
        private CommandDispatcherInterface $commandDispatcher,
    ) {
    }

    #[Route('/quiz', name: 'quiz_create', methods: 'POST')]
    public function __invoke(#[MapRequestPayload] CreateQuizRequestModel $data, Request $request): JsonResponse
    {
        $this->commandDispatcher->dispatch(new CreateQuizCommand(
            $id = QuizId::create(),
            $data->numberOfQuestions,
            $data->duration,
            $data->passingScore
        ));

        return new JsonResponse(['id' => $id->getValue()]);
    }
}
