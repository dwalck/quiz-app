<?php

declare(strict_types=1);

namespace App\Tests\Unit\SharedKernel\Infrastructure\Symfony;

use App\SharedKernel\Infrastructure\Symfony\CommandDispatcher;
use App\SharedKernel\Infrastructure\Symfony\QueryDispatcher;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

/**
 * @internal
 */
#[CoversClass(CommandDispatcher::class)]
final class CommandDispatcherTest extends TestCase
{
    public function testDispatchWillDispatchOnMessageBus(): void
    {
        $messageBus = $this->createMock(MessageBusInterface::class);
        $messageBus
            ->expects($this->once())
            ->method('dispatch')
            ->with($message = (object) ['test' => '1'])
            ->willReturn($this->createEnvelope())
        ;

        $instance = $this->createInstance($messageBus);
        $instance->dispatch($message);
    }

    private function createInstance(
        ?MessageBusInterface $messageBus = null,
    ): QueryDispatcher {
        return new QueryDispatcher(
            $messageBus ?? $this->createStub(MessageBusInterface::class),
        );
    }

    private function createEnvelope(array $stamps = [
        new HandledStamp((object) [], 'handler'),
    ]): Envelope
    {
        return new Envelope(
            (object) [],
            $stamps
        );
    }
}
