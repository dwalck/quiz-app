<?php

declare(strict_types=1);

namespace App\Quiz\Application\Command\CreateQuiz;

use Symfony\Component\Messenger\Attribute\AsMessage;
use Symfony\Component\Uid\Uuid;

#[AsMessage]
final class CreateQuizCommand
{
    public function __construct(
        public Uuid $quizId,
        public int $questionsCount,
        public int $duration = 60,
        public int $passingScore = 70,
    ) {
    }
}
