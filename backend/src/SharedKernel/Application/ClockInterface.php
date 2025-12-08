<?php

namespace App\SharedKernel\Application;

interface ClockInterface
{
    public function now(): \DateTimeImmutable;
}
