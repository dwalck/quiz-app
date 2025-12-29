<?php

declare(strict_types=1);

namespace App\Tests\Unit\Quiz\Domain;

use App\Quiz\Domain\Enum\QuizState;
use App\Quiz\Domain\Quiz;
use App\Quiz\Domain\QuizStateChange;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

/**
 * @internal
 */
#[CoversClass(QuizStateChange::class)]
final class QuizStateChangeTest extends TestCase
{
    public function testConstructorSetValidValues(): void
    {
        $instance = $this->createInstance(
            $id = Uuid::v4(),
            $quiz = $this->createMock(Quiz::class),
            $state = QuizState::STARTED,
            $changedAt = new \DateTimeImmutable('now')
        );

        $this->assertSame($id, $instance->getId());
        $this->assertSame($quiz, $instance->getQuiz());
        $this->assertSame($state, $instance->getState());
        $this->assertSame($changedAt, $instance->getChangedAt());
    }

    private function createInstance(
        ?Uuid $id = null,
        ?Quiz $quiz = null,
        ?QuizState $state = QuizState::STARTED,
        ?\DateTimeImmutable $changedAt = new \DateTimeImmutable('now'),
    ): QuizStateChange {
        return new QuizStateChange(
            $id ?? Uuid::v4(),
            $quiz ?? $this->createMock(Quiz::class),
            $state,
            $changedAt
        );
    }
}
