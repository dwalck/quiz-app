<?php

declare(strict_types=1);

namespace App\Quiz\Domain;

use App\Quiz\Domain\Enum\QuizState;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Cache('READ_ONLY')]
#[ORM\Entity]
readonly class QuizStateChange
{
    #[
        ORM\Id,
        ORM\Column(type: UuidType::NAME)
    ]
    private Uuid $id;

    #[ORM\Column(type: Types::STRING, length: 32, enumType: QuizState::class)]
    private QuizState $state;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $changedAt;

    public function __construct(
        Uuid $id,
        QuizState $state,
        \DateTimeImmutable $changedAt,
    ) {
        $this->id = $id;
        $this->state = $state;
        $this->changedAt = $changedAt;
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getState(): QuizState
    {
        return $this->state;
    }

    public function getChangedAt(): \DateTimeImmutable
    {
        return $this->changedAt;
    }
}
