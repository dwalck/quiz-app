<?php

declare(strict_types=1);

namespace App\Quiz\Application\Command\CreateQuiz;

use App\Quiz\Domain\ValueObject\QuizId;
use Symfony\Component\Messenger\Attribute\AsMessage;

#[AsMessage]
final class CreateQuizCommand
{
    public function __construct(
        public QuizId $quizId,
        public int $questionsCount,
        public int $duration = 60,
        public int $passingScore = 70,
    ) {
    }
}
