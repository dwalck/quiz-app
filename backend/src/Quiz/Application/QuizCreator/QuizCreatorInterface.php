<?php

namespace App\Quiz\Application\QuizCreator;

use App\Quiz\Domain\Quiz;

interface QuizCreatorInterface
{
    public function create(
        int $questionsCount,
        int $duration,
        int $passingScore
    ): Quiz;
}
