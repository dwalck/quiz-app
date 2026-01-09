<?php

declare(strict_types=1);

namespace App\SharedKernel\Infrastructure\Symfony;

use App\SharedKernel\Application\QueryDispatcherInterface;
use App\SharedKernel\Infrastructure\Symfony\Exception\QueryNotHandledException;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Webmozart\Assert\Assert;

final readonly class QueryDispatcher implements QueryDispatcherInterface
{
    public function __construct(
        private MessageBusInterface $messageBus,
    ) {
    }

    public function dispatch(object $command): object
    {
        try {
            $envelope = $this->messageBus->dispatch($command);
        } catch (HandlerFailedException $e) {
            $exceptions = $e->getWrappedExceptions();

            Assert::notEmpty($exceptions);

            throw \reset($exceptions);
        }

        if (null === $envelope->last(HandledStamp::class)) {
            throw new QueryNotHandledException($command);
        }

        return $envelope->last(HandledStamp::class)->getResult();
    }
}
