<?php

namespace App\Quiz\Application\GetQuestions;

readonly class GetQuestionsQuery
{
    public function __construct(
        public int $questionsCount
    )
    {
    }
}
