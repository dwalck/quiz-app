<?php

namespace App\SharedKernel\Application;

interface EventDispatcherInterface
{
    public function dispatch(object $event): void;
}
