<?php

declare(strict_types=1);

namespace App\Quiz\Application\Query\GetQuiz;

use App\Quiz\Application\Query\GetQuiz\Exception\QuizNotFoundException;
use App\Quiz\Application\Query\GetQuiz\Model\QuizConfigurationModel;
use App\Quiz\Application\Query\GetQuiz\Model\QuizModel;
use App\Quiz\Application\Query\GetQuiz\Model\QuizQuestionAnswerModel;
use App\Quiz\Application\Query\GetQuiz\Model\QuizQuestionModel;
use App\Quiz\Domain\Question;
use App\Quiz\Domain\QuestionAnswer;
use App\Quiz\Domain\Quiz;
use App\Quiz\Domain\QuizQuestion;
use App\Quiz\Domain\Repository\QuizRepositoryInterface;
use App\SharedKernel\Domain\Exception\EntityNotFoundException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class GetQuizHandler
{
    public function __construct(
        private QuizRepositoryInterface $repository,
    ) {
    }

    public function __invoke(GetQuizQuery $query): QuizModel
    {
        try {
            $quiz = $this->repository->get($query->quizId);
        } catch (EntityNotFoundException $e) {
            throw new QuizNotFoundException();
        }

        return new QuizModel(
            $quiz->getId(),
            new QuizConfigurationModel(
                $quiz->getConfiguration()->getDuration(),
                $quiz->getConfiguration()->getPassingScore(),
            ),
            $this->extractQuestions($quiz)
        );
    }

    /**
     * @return array<QuizQuestionModel>
     */
    private function extractQuestions(Quiz $quiz): array
    {
        return \array_map(function (QuizQuestion $question) {
            return new QuizQuestionModel(
                $question->getId(),
                $question->getQuestion()->getContent(),
                $this->extractQuestionAnswer($question->getQuestion()),
            );
        }, $quiz->getQuestions());
    }

    /**
     * @return array<QuizQuestionAnswerModel>
     */
    private function extractQuestionAnswer(Question $question): array
    {
        return \array_map(function (QuestionAnswer $question) {
            return new QuizQuestionAnswerModel(
                $question->getId(),
                $question->getContent(),
            );
        }, $question->getAnswers());
    }
}
