<?php

declare(strict_types=1);

namespace App\Quiz\Application\Command\CreateQuiz;

final class CreateQuizCommand
{
    public function __construct(
        public int $questionsCount,
        public int $duration = 60,
        public int $passingScore = 70,
    ) {
    }
}
