<?php

declare(strict_types=1);

namespace App\SharedKernel\Infrastructure\Symfony;

use App\SharedKernel\Application\QueryDispatcherInterface;
use App\SharedKernel\Infrastructure\Symfony\Exception\QueryNotHandledException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

final readonly class QueryDispatcher implements QueryDispatcherInterface
{
    public function __construct(
        private MessageBusInterface $messageBus,
    ) {
    }

    public function dispatch(object $command): object
    {
        $envelope = $this->messageBus->dispatch($command);

        if (null === $envelope->last(HandledStamp::class)) {
            throw new QueryNotHandledException($command);
        }

        return $envelope->last(HandledStamp::class)->getResult();
    }
}
