<?php

declare(strict_types=1);

namespace App\SharedKernel\Application;

interface QueryDispatcherInterface
{
    public function dispatch(object $command): object;
}
