<?php

declare(strict_types=1);

namespace App\SharedKernel\Domain;

use Symfony\Component\Uid\Uuid;

abstract class AbstractId
{
    protected function __construct(
        protected readonly Uuid $value,
    ) {
    }

    public static function create(): self
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
}
