<?php

declare(strict_types=1);

namespace App\Quiz\Application\Command\CreateQuiz;

use App\Quiz\Application\Service\QuestionSelection\QuizQuestionsSelectionServiceInterface;
use App\Quiz\Domain\Quiz;
use App\Quiz\Domain\QuizConfiguration;
use App\Quiz\Domain\QuizQuestion;
use App\Quiz\Domain\Repository\QuizRepositoryInterface;
use App\Quiz\Domain\ValueObject\QuizQuestionId;
use App\SharedKernel\Application\ClockInterface;
use App\SharedKernel\Application\EventDispatcherInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class CreateQuizHandler
{
    public function __construct(
        private QuizRepositoryInterface $quizRepository,
        private EventDispatcherInterface $eventDispatcher,
        private ClockInterface $clock,
        private QuizQuestionsSelectionServiceInterface $quizQuestionsSelectionService,
    ) {
    }

    public function __invoke(CreateQuizCommand $command): void
    {
        $questions = $this->quizQuestionsSelectionService->select($command->questionsCount);

        $quiz = new Quiz(
            $command->quizId,
            new QuizConfiguration(
                $command->duration,
                $command->passingScore
            ),
            $this->clock->now()
        );

        foreach ($questions as $question) {
            $quiz->addQuestion(new QuizQuestion(
                QuizQuestionId::create(),
                $quiz,
                $question
            ));
        }

        foreach ($quiz->pullEvents() as $event) {
            $this->eventDispatcher->dispatch($event);
        }

        $this->quizRepository->save($quiz);
    }
}
