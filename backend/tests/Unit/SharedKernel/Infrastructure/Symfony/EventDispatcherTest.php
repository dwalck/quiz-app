<?php

declare(strict_types=1);

namespace App\Tests\Unit\SharedKernel\Infrastructure\Symfony;

use App\SharedKernel\Infrastructure\Symfony\EventDispatcher;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface as SymfonyEventDispatcherInterface;

/**
 * @internal
 */
#[CoversClass(EventDispatcher::class)]
final class EventDispatcherTest extends TestCase
{
    public function testDispatchWillDispatchOnEventDispatcher(): void
    {
        $eventDispatcher = $this->createMock(SymfonyEventDispatcherInterface::class);
        $eventDispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->with($message = (object) ['test' => '1'])
            ->willReturn($this->createEnvelope())
        ;

        $instance = $this->createInstance($eventDispatcher);
        $instance->dispatch($message);
    }

    private function createInstance(
        ?SymfonyEventDispatcherInterface $eventDispatcher = null,
    ): EventDispatcher {
        return new EventDispatcher(
            $eventDispatcher ?? $this->createStub(SymfonyEventDispatcherInterface::class),
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
