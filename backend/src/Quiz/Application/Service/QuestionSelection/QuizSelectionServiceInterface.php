<?php

declare(strict_types=1);

namespace App\Quiz\Application\Service\QuestionSelection;

use App\Quiz\Domain\Question;

interface QuizSelectionServiceInterface
{
    /**
     * @return array<Question>
     */
    public function select(int $count): array;
}
