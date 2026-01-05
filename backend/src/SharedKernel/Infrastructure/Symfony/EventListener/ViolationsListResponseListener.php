<?php

declare(strict_types=1);

namespace App\SharedKernel\Infrastructure\Symfony\EventListener;

use App\SharedKernel\Infrastructure\Symfony\ViolationsListResponseCreator;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Validator\Exception\ValidationFailedException;

#[AsEventListener]
final readonly class ViolationsListResponseListener
{
    public function __construct(
        private ViolationsListResponseCreator $violationsListResponseCreator,
    ) {
    }

    public function __invoke(ExceptionEvent $event): void
    {
        $throwable = $event->getThrowable();

        if ('json' !== $event->getRequest()->getContentTypeFormat()) {
            return;
        }

        if (!($throwable instanceof UnprocessableEntityHttpException)) {
            return;
        }

        $previous = $throwable->getPrevious();
        if (!($previous instanceof ValidationFailedException)) {
            return;
        }

        $response = new JsonResponse(
            $this->violationsListResponseCreator->create($previous->getViolations()),
            422
        );

        $event->setResponse($response);
    }
}
