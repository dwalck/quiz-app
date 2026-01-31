<?php

declare(strict_types=1);

namespace App\Quiz\Application\Command\StartQuiz;

use App\Quiz\Infrastructure\Doctrine\Repository\QuizRepository;
use App\SharedKernel\Application\ClockInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

#[AsMessageHandler]
final readonly class StartQuizHandler
{
    public function __construct(
        private QuizRepository $quizRepository,
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
