<?php

declare(strict_types=1);

namespace App\Quiz\Infrastructure\Symfony\Controller;

use App\Quiz\Application\Query\GetQuiz\Exception\QuizNotFoundException;
use App\Quiz\Application\Query\GetQuiz\GetQuizQuery;
use App\Quiz\Domain\ValueObject\QuizId;
use App\SharedKernel\Application\QueryDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
final readonly class GetQuizController
{
    public function __construct(
        private QueryDispatcherInterface $queryDispatcher,
    ) {
    }

    #[Route('/quiz/{id}', name: 'quiz_get', methods: 'GET')]
    public function __invoke(string $id): JsonResponse
    {
        try {
            return new JsonResponse($this->queryDispatcher->dispatch(
                new GetQuizQuery(
                    QuizId::fromString($id)
                )
            ));
        } catch (QuizNotFoundException) {
            return new JsonResponse((object) [], status: Response::HTTP_NOT_FOUND);
        }
    }
}
