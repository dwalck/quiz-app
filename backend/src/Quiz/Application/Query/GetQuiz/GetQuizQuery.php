<?php

declare(strict_types=1);

namespace App\Quiz\Application\Query\GetQuiz;

use App\Quiz\Domain\ValueObject\QuizId;

final readonly class GetQuizQuery
{
    public function __construct(
        public QuizId $quizId,
    ) {
    }
}
