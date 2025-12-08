<?php

namespace App\Quiz\Domain\Repository;

use App\Quiz\Domain\Quiz;

interface QuizRepositoryInterface
{
    public function save(Quiz $quiz): void;
}
