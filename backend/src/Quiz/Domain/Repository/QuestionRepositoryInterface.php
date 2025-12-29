<?php

declare(strict_types=1);

namespace App\Quiz\Domain\Repository;

use App\Quiz\Domain\Question;
use Symfony\Component\Uid\Uuid;

interface QuestionRepositoryInterface
{
    /**
     * @param array<Uuid> $ids
     *
     * @return array<Question>
     */
    public function findByIds(array $ids): array;

    /**
     * @return array<Uuid>
     */
    public function getAllIds(): array;
}
