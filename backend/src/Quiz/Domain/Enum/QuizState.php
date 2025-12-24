<?php

declare(strict_types=1);

namespace App\Quiz\Domain\Enum;

enum QuizState: string
{
    case CREATED = 'created';

    case STARTED = 'started';

    case FINISHED = 'finished';
}
