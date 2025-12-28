<?php

declare(strict_types=1);

namespace App\SharedKernel\Infrastructure\Symfony;

use Symfony\Component\Messenger\MessageBusInterface;

final readonly class CommandDispatcher
{
    public function __construct(
        private MessageBusInterface $messageBus,
    ) {
    }

    public function dispatch(object $command): object
    {
        return $this->messageBus->dispatch($command);
    }
}
