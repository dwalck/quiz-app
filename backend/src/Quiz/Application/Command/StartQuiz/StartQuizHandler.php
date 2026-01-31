<?php

declare(strict_types=1);

namespace App\Quiz\Application\Command\StartQuiz;

use App\Quiz\Domain\Repository\QuizRepositoryInterface;
use App\SharedKernel\Application\ClockInterface;
use App\SharedKernel\Application\EventDispatcherInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class StartQuizHandler
{
    public function __construct(
        private QuizRepositoryInterface $quizRepository,
        private ClockInterface $clock,
        private EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function __invoke(StartQuizCommand $command): void
    {
        $quiz = $this->quizRepository->get($command->quizId);
        $quiz->start($this->clock->now());

        foreach ($quiz->pullEvents() as $event) {
            $this->eventDispatcher->dispatch($event);
        }

        $this->quizRepository->save($quiz);
    }
}
