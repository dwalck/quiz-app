<?php

declare(strict_types=1);

namespace App\Tests\Unit\SharedKernel\Infrastructure\Symfony;

use App\SharedKernel\Infrastructure\Symfony\Exception\QueryNotHandledException;
use App\SharedKernel\Infrastructure\Symfony\QueryDispatcher;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Messenger\Stamp\ReceivedStamp;
use Symfony\Component\Messenger\Stamp\SentStamp;

/**
 * @internal
 */
#[CoversClass(QueryDispatcher::class)]
#[UsesClass(Envelope::class)]
final class QueryDispatcherTest extends TestCase
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

    public function testDispatchWillReturnsHandledStampResult(): void
    {
        $messageBus = $this->createStub(MessageBusInterface::class);
        $messageBus
            ->method('dispatch')
            ->willReturn($this->createEnvelope([
                new SentStamp('handler'),
                new HandledStamp($stampResult = (object) ['stamp' => 'handled'], 'handler'),
                new ReceivedStamp('handler'),
            ]))
        ;

        $instance = $this->createInstance($messageBus);

        $this->assertSame($stampResult, $instance->dispatch((object) []));
    }

    public function testDispatchWillThrowQueryNotHandledException(): void
    {
        $messageBus = $this->createStub(MessageBusInterface::class);
        $messageBus
            ->method('dispatch')
            ->willReturn($this->createEnvelope([
                new SentStamp('handler'),
            ]))
        ;
        $instance = $this->createInstance($messageBus);

        $this->expectException(QueryNotHandledException::class);

        $instance->dispatch((object) []);
    }

    public function testDispatchWillThrowHandlerFailedExceptionFirstWrappedException(): void
    {
        $messageBus = $this->createStub(MessageBusInterface::class);
        $messageBus
            ->method('dispatch')
            ->willThrowException(new HandlerFailedException(
                $this->createEnvelope(),
                [
                    $exception1 = $this->createStub(\Throwable::class),
                    $this->createStub(\Exception::class),
                    $this->createStub(\Throwable::class),
                ]
            ))
        ;

        $instance = $this->createInstance($messageBus);

        try {
            $instance->dispatch((object) []);
        } catch (\Throwable $exception) {
            $this->assertSame($exception1, $exception);
        }
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
