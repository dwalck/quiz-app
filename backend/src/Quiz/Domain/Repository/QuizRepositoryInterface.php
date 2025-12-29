<?php

declare(strict_types=1);

namespace App\Quiz\Domain\Repository;

use App\Quiz\Domain\Quiz;
use App\Quiz\Domain\ValueObject\QuizId;

interface QuizRepositoryInterface
{
    public function get(QuizId $id): Quiz;

    public function save(Quiz $quiz): void;
}
