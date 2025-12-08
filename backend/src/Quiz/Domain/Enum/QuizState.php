<?php

namespace App\Quiz\Domain\Enum;

enum QuizState: string
{
    case CREATED = 'created';

    case STARTED = 'started';

    case FINISHED = 'finished';
}
