<?php

declare(strict_types=1);

namespace App\SharedKernel\Application;

interface EventDispatcherInterface
{
    public function dispatch(object $event): void;
}
