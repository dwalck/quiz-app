<?php

declare(strict_types=1);

namespace App\SharedKernel\Application;

interface CommandDispatcherInterface
{
    public function dispatch(object $command): object;
}
