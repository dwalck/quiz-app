<?php

namespace App\Quiz\Application\QuizCreator;

use App\Quiz\Domain\Event\QuizCreatedEvent;
use App\Quiz\Domain\Event\QuizStartEvent;
use App\Quiz\Domain\Question;
use App\Quiz\Domain\Quiz;
use App\Quiz\Domain\QuizConfiguration;
use App\Quiz\Domain\QuizQuestion;
use App\Quiz\Domain\Repository\QuestionRepositoryInterface;
use App\Quiz\Domain\Repository\QuizRepositoryInterface;
use App\SharedKernel\Application\ClockInterface;
use App\SharedKernel\Application\EventDispatcherInterface;
use Symfony\Component\Uid\Uuid;

final readonly class QuizCreator implements QuizCreatorInterface
{
    public function __construct(
        private QuestionRepositoryInterface $questionRepository,
        private QuizRepositoryInterface $quizRepository,
        private EventDispatcherInterface $eventDispatcher,
        private ClockInterface $clock,
    )
    {
    }

    public function create(
        int $questionsCount,
        int $duration = 60,
        int $passingScore = 70
    ): Quiz
    {
        $quiz = new Quiz(
            Uuid::v4(),
            new QuizConfiguration(
                $duration,
                $passingScore
            ),
            $this->clock->now()
        );

        $questions = $this->getQuestions($questionsCount);
        foreach ($questions as $question) {
            $quiz->addQuestion(new QuizQuestion(
                Uuid::v4(),
                $quiz,
                $question
            ));
        }

        $this->eventDispatcher->dispatch(new QuizCreatedEvent($quiz));

        $quiz->makeStarted($this->clock->now());

        $this->quizRepository->save($quiz);

        $this->eventDispatcher->dispatch(new QuizStartEvent($quiz));

        return $quiz;
    }

    /**
     * @return array<Question>
     */
    private function getQuestions(int $count): array
    {
        $allIds = $this->questionRepository->getAllIds();

        $count = min($count, count($allIds));

        $keys = array_rand($allIds, $count);

        $ids = [];
        foreach ($keys as $key) {
            $ids[] = $allIds[$key];
        }

        return $this->questionRepository->findByIds($ids);
    }
}
