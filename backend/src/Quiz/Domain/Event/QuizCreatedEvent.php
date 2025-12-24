<?php

declare(strict_types=1);

namespace App\Quiz\Domain\Event;

use App\Quiz\Domain\Quiz;

final readonly class QuizCreatedEvent
{
    public function __construct(
        public Quiz $quiz,
    ) {
    }
}
