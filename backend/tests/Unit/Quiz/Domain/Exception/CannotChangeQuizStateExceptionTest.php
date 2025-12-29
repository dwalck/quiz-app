<?php

declare(strict_types=1);

namespace App\Tests\Unit\Quiz\Domain\Exception;

use App\Quiz\Domain\Enum\QuizState;
use App\Quiz\Domain\Exception\CannotChangeQuizStateException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(CannotChangeQuizStateException::class)]
final class CannotChangeQuizStateExceptionTest extends TestCase
{
    public function testExceptionMessageIsValid(): void
    {
        $this->expectExceptionMessage('Cannot change quiz state from "STARTED" to "FINISHED".');

        throw new CannotChangeQuizStateException(QuizState::STARTED, QuizState::FINISHED);
    }
}
