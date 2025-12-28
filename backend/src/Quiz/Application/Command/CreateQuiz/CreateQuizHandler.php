<?php

declare(strict_types=1);

namespace App\Quiz\Application\Command\CreateQuiz;

use App\Quiz\Application\Service\QuestionSelection\QuizSelectionServiceInterface;
use App\Quiz\Domain\Event\QuizCreatedEvent;
use App\Quiz\Domain\Quiz;
use App\Quiz\Domain\QuizConfiguration;
use App\Quiz\Domain\QuizQuestion;
use App\Quiz\Domain\Repository\QuizRepositoryInterface;
use App\SharedKernel\Application\ClockInterface;
use App\SharedKernel\Application\EventDispatcherInterface;
use Symfony\Component\Uid\Uuid;

final readonly class CreateQuizHandler
{
    public function __construct(
        private QuizRepositoryInterface $quizRepository,
        private EventDispatcherInterface $eventDispatcher,
        private ClockInterface $clock,
        private QuizSelectionServiceInterface $quizSelectionService,
    ) {
    }

    public function __invoke(CreateQuizCommand $command): Quiz
    {
        $questions = $this->quizSelectionService->select($command->questionsCount);

        $quiz = new Quiz(
            Uuid::v4(),
            new QuizConfiguration(
                $command->duration,
                $command->passingScore
            ),
            $this->clock->now()
        );

        foreach ($questions as $question) {
            $quiz->addQuestion(new QuizQuestion(
                Uuid::v4(),
                $quiz,
                $question
            ));
        }

        $this->eventDispatcher->dispatch(new QuizCreatedEvent($quiz));

        $this->quizRepository->save($quiz);

        return $quiz;
    }
}
