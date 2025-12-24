<?php

declare(strict_types=1);

namespace App\Quiz\Application\GetQuestions;

readonly class GetQuestionsQuery
{
    public function __construct(
        public int $questionsCount,
    ) {
    }
}
