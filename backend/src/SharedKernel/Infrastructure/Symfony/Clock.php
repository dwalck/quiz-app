<?php

declare(strict_types=1);

namespace App\SharedKernel\Infrastructure\Symfony;

use App\SharedKernel\Application\ClockInterface;
use Symfony\Component\Clock\ClockInterface as SymfonyClockInterface;

final readonly class Clock implements ClockInterface
{
    public function __construct(
        private SymfonyClockInterface $clock,
    ) {
    }

    public function now(): \DateTimeImmutable
    {
        return $this->clock->now();
    }
}
