<?php

declare(strict_types=1);

namespace App\SharedKernel\Infrastructure\Symfony;

use App\SharedKernel\Application\QueryDispatcherInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final readonly class QueryDispatcher implements QueryDispatcherInterface
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
