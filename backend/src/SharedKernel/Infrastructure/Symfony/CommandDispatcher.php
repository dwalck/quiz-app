<?php

declare(strict_types=1);

namespace App\SharedKernel\Infrastructure\Symfony;

use App\SharedKernel\Application\CommandDispatcherInterface;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Webmozart\Assert\Assert;

final readonly class CommandDispatcher implements CommandDispatcherInterface
{
    public function __construct(
        private MessageBusInterface $messageBus,
    ) {
    }

    public function dispatch(object $command): void
    {
        try {
            $this->messageBus->dispatch($command);
        } catch (HandlerFailedException $e) {
            $exceptions = $e->getWrappedExceptions();

            Assert::notEmpty($exceptions);

            throw \reset($exceptions);
        }
    }
}
