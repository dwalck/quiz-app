<?php

namespace App\Tests\Unit\Quiz\Domain;

use App\Quiz\Domain\Enum\QuizState;
use App\Quiz\Domain\QuizStateChange;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

#[CoversClass(QuizStateChange::class)]
final class QuizStateChangeTest extends TestCase
{
    public function test_constructor_set_valid_values(): void
    {
        $instance = $this->createInstance(
            $id = Uuid::v4(),
            $state = QuizState::STARTED,
            $changedAt = new \DateTimeImmutable('now')
        );

        $this->assertSame($id, $instance->getId());
        $this->assertSame($state, $instance->getState());
        $this->assertSame($changedAt, $instance->getChangedAt());
    }

    private function createInstance(
        ?Uuid $id = null,
        ?QuizState $state = QuizState::STARTED,
        ?\DateTimeImmutable $changedAt = new \DateTimeImmutable('now'),
    ): QuizStateChange
    {
        return new QuizStateChange(
            $id ?? Uuid::v4(),
            $state,
            $changedAt
        );
    }
}
