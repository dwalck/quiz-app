<?php

declare(strict_types=1);

namespace App\Quiz\Application\Command\StartQuiz;

use App\Quiz\Domain\ValueObject\QuizId;
use Symfony\Component\Messenger\Attribute\AsMessage;

#[AsMessage]
final readonly class StartQuizCommand
{
    public function __construct(
        public QuizId $quizId,
    ) {
    }
}
