<?php

declare(strict_types=1);

namespace App\Quiz\Domain\Repository;

use App\Quiz\Domain\Quiz;

interface QuizRepositoryInterface
{
    public function save(Quiz $quiz): void;
}
