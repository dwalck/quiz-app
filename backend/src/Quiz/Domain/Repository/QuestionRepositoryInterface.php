<?php

declare(strict_types=1);

namespace App\Quiz\Domain\Repository;

use Symfony\Component\Uid\Uuid;

interface QuestionRepositoryInterface
{
    /**
     * @param array<Uuid> $ids
     */
    public function findByIds(array $ids): array;

    /**
     * @return array<Uuid>
     */
    public function getAllIds(): array;
}
