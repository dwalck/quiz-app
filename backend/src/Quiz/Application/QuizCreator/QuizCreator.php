<?php

namespace App\Quiz\Application\QuizCreator;

use App\Quiz\Domain\Event\QuizCreatedEvent;
use App\Quiz\Domain\Question;
use App\Quiz\Domain\Quiz;
use App\Quiz\Domain\QuizConfiguration;
use App\Quiz\Domain\QuizQuestion;
use App\Quiz\Domain\Repository\QuizRepositoryInterface;
use App\SharedKernel\Application\ClockInterface;
use App\SharedKernel\Application\EventDispatcherInterface;
use Symfony\Component\Uid\Uuid;
use Webmozart\Assert\Assert;

final readonly class QuizCreator implements QuizCreatorInterface
{
    public function __construct(
        private QuizRepositoryInterface $quizRepository,
        private EventDispatcherInterface $eventDispatcher,
        private ClockInterface $clock,
    )
    {
    }

    /**
     * @param array<Question> $questions
     */
    public function create(
        array $questions,
        int $duration = 60,
        int $passingScore = 70
    ): Quiz
    {
        Assert::allIsInstanceOf($questions, Question::class);
        Assert::notEmpty($questions, 'Quiz must contain at least one question.');

        $quiz = new Quiz(
            Uuid::v4(),
            new QuizConfiguration(
                $duration,
                $passingScore
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
