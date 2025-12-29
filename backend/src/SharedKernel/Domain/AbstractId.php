<?php

declare(strict_types=1);

namespace App\SharedKernel\Domain;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

/**
 * @phpstan-consistent-constructor
 */
abstract class AbstractId
{
    #[
        ORM\Id,
        ORM\Column(name: 'id', type: UuidType::NAME)
    ]
    protected readonly Uuid $value;

    protected function __construct(
        Uuid $value,
    ) {
        $this->value = $value;
    }

    public static function create(): static
    {
        return new static(Uuid::v4());
    }

    public static function fromString(string $value): static
    {
        return new static(Uuid::fromString($value));
    }

    public function equals(self $other): bool
    {
        return static::class === $other::class
            && $this->value->equals($other->value);
    }

    public function getValue(): Uuid
    {
        return $this->value;
    }
}
