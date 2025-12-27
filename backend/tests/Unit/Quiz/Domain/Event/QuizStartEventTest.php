<?php

namespace App\Tests\Unit\Quiz\Domain\Event;

use App\Quiz\Domain\Event\QuizCreatedEvent;
use App\Quiz\Domain\Event\QuizStartEvent;
use App\Quiz\Domain\Quiz;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(QuizStartEvent::class)]
final class QuizStartEventTest extends TestCase
{
    public function test_constructor_set_valid_values(): void
    {
        $quiz = $this->createMock(Quiz::class);

        $this->assertSame(
            $quiz,
            $this->createInstance($quiz)->quiz
        );
    }

    public function test_quiz_value_is_not_replaceable(): void
    {
        $instance = $this->createInstance();

        $quiz = $this->createMock(Quiz::class);
        try {
            $instance->quiz = $this->createMock(Quiz::class);
        } catch (\Error) {}

        $this->assertNotSame($quiz, $instance->quiz);
    }

    private function createInstance(
        ?Quiz $quiz = null,
    ): QuizStartEvent
    {
        return new QuizStartEvent(
            $quiz ?? $this->createMock(Quiz::class),
        );
    }
}
