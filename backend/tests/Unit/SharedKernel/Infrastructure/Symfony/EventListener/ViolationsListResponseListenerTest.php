<?php

declare(strict_types=1);

namespace App\Tests\Unit\SharedKernel\Infrastructure\Symfony\EventListener;

use App\SharedKernel\Infrastructure\Symfony\EventListener\ViolationsListResponseListener;
use App\SharedKernel\Infrastructure\Symfony\Model\ViolationsList\Response\ViolationResponseModel;
use App\SharedKernel\Infrastructure\Symfony\Model\ViolationsList\Response\ViolationsListResponseModel;
use App\SharedKernel\Infrastructure\Symfony\ViolationsListResponseCreator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Exception\ValidationFailedException;

/**
 * @internal
 */
#[CoversClass(ViolationsListResponseListener::class)]
final class ViolationsListResponseListenerTest extends TestCase
{
    public function testWontSetResponseIfEventExceptionIsSimpleThrowable(): void
    {
        $this->doInvoke($event = $this->createEvent($this->createStub(\Throwable::class)));

        $this->assertFalse($event->hasResponse());
    }

    public function testWontSetResponseIfEventExceptionPreviousIsSimpleThrowable(): void
    {
        $this->doInvoke($event = $this->createEvent(
            $this->createUnprocessableEntityHttpException(
                $this->createStub(\Throwable::class)
            )
        ));

        $this->assertFalse($event->hasResponse());
    }

    public function testWontSetResponseIfRequestFormatTypeIsNotJson(): void
    {
        $event = $this->createEvent(
            $this->createUnprocessableEntityHttpException(
                $this->createValidationFailedException()
            ),
            $this->createRequest('form')
        );

        $this->doInvoke($event);

        $this->assertFalse($event->hasResponse());
    }

    public function testWillSetResponse(): void
    {
        $event = $this->createEvent(
            $this->createUnprocessableEntityHttpException(
                $this->createValidationFailedException($violationList = $this->createStub(ConstraintViolationListInterface::class))
            )
        );

        $violationsListResponseCreator = $this->createMock(ViolationsListResponseCreator::class);
        $violationsListResponseCreator
            ->expects($this->once())
            ->method('create')
            ->with($violationList)
            ->willReturn($data = new ViolationsListResponseModel([
                new ViolationResponseModel('test', 'Lorem ipsum'),
            ]))
        ;

        $this->doInvoke($event, $violationsListResponseCreator);

        $this->assertEquals(\json_encode($data), $event->getResponse()->getContent());
        $this->assertEquals(422, $event->getResponse()->getStatusCode());
    }

    private function createEvent(?\Throwable $exception = null, ?Request $request = null): ExceptionEvent
    {
        return new ExceptionEvent(
            $this->createMock(HttpKernelInterface::class),
            $request ?? $this->createRequest('json'),
            1,
            $exception ?? $this->createUnprocessableEntityHttpException()
        );
    }

    private function createUnprocessableEntityHttpException(?\Throwable $previous = null): UnprocessableEntityHttpException
    {
        return new UnprocessableEntityHttpException(
            previous: $previous ?? $this->createValidationFailedException()
        );
    }

    private function createValidationFailedException(?ConstraintViolationListInterface $violationList = null): ValidationFailedException
    {
        return new ValidationFailedException(
            'test',
            $violationList ?? $this->createStub(ConstraintViolationListInterface::class),
        );
    }

    private function doInvoke(
        ExceptionEvent $event,
        ?ViolationsListResponseCreator $violationsListResponseCreator = null,
    ): void {
        new ViolationsListResponseListener(
            $violationsListResponseCreator ?? $this->createStub(ViolationsListResponseCreator::class),
        )($event);
    }

    private function createRequest(string $contentTypeFormat): Request
    {
        $request = $this->createStub(Request::class);
        $request->method('getContentTypeFormat')->willReturn($contentTypeFormat);

        return $request;
    }
}
