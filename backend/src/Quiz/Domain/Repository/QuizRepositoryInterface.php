<?php

declare(strict_types=1);

namespace App\Quiz\Domain\Repository;

use App\Quiz\Domain\Quiz;
use Symfony\Component\Uid\Uuid;

interface QuizRepositoryInterface
{
    public function get(Uuid $id): Quiz;

    public function save(Quiz $quiz): void;
}
