<?php

declare(strict_types=1);

namespace App\Quiz\Domain\Repository;

use App\Quiz\Domain\Question;
use App\Quiz\Domain\ValueObject\QuestionId;

interface QuestionRepositoryInterface
{
    public function save(Question $question): void;

    /**
     * @param array<QuestionId> $ids
     *
     * @return array<Question>
     */
    public function findByIds(array $ids): array;

    /**
     * @return array<QuestionId>
     */
    public function getAllIds(): array;
}
