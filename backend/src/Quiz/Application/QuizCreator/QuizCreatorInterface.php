<?php

declare(strict_types=1);

namespace App\Quiz\Application\QuizCreator;

use App\Quiz\Domain\Question;
use App\Quiz\Domain\Quiz;

interface QuizCreatorInterface
{
    /**
     * @param array<Question> $questions
     */
    public function create(array $questions, int $duration, int $passingScore): Quiz;
}
