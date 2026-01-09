<?php

declare(strict_types=1);

namespace App\SharedKernel\Infrastructure\Symfony;

use App\SharedKernel\Application\CommandDispatcherInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final readonly class CommandDispatcher implements CommandDispatcherInterface
{
    public function __construct(
        private MessageBusInterface $messageBus,
    ) {
    }

    public function dispatch(object $command): void
    {
        $this->messageBus->dispatch($command);
    }
}
