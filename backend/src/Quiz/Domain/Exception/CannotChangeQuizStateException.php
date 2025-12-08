<?php

namespace App\Quiz\Domain\Exception;

use App\Quiz\Domain\Enum\QuizState;

final class CannotChangeQuizStateException extends \DomainException
{
    public function __construct(QuizState $quizState, QuizState $newState)
    {
        parent::__construct(sprintf('Cannot change quiz state from "%s" to "%s".', $quizState->name, $newState->name));
    }
}
