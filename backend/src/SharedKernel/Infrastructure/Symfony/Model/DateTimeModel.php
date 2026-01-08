<?php

declare(strict_types=1);

namespace App\SharedKernel\Infrastructure\Symfony\Model;

class DateTimeModel implements \JsonSerializable
{
    public function __construct(private readonly \DateTimeImmutable $dateTime)
    {
    }

    public function jsonSerialize(): string
    {
        return $this->dateTime->format(\DateTimeImmutable::ATOM);
    }
}
