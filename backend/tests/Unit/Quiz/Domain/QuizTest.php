<?php

declare(strict_types=1);

namespace App\Tests\Unit\Quiz\Domain;

use App\Quiz\Domain\Enum\QuizState;
use App\Quiz\Domain\Exception\CannotChangeQuizStateException;
use App\Quiz\Domain\Quiz;
use App\Quiz\Domain\QuizConfiguration;
use App\Quiz\Domain\QuizQuestion;
use App\Quiz\Domain\ValueObject\QuizId;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(Quiz::class)]
final class QuizTest extends TestCase
{
    private Quiz $baseInstance;

    private Quiz $stateStartedInstance;

    private Quiz $stateFinishedInstance;

    protected function setUp(): void
    {
        $this->baseInstance = $this->createInstance();

        $this->stateStartedInstance = $this->createInstance();

        $this->stateFinishedInstance = $this->createInstance();
        $this->stateFinishedInstance->finish(new \DateTimeImmutable('+7 days'));
    }

    public function testConstructor(): void
    {
        $instance = $this->createInstance(
            $id = QuizId::create(),
            $configuration = $this->createMock(QuizConfiguration::class),
            $startedAt = new \DateTimeImmutable(),
        );

        $this->assertSame($id, $instance->getId());
        $this->assertSame($configuration, $instance->getConfiguration());
        $this->assertSame($startedAt, $instance->getStartedAt());
        $this->assertEmpty($instance->getQuestions());
    }

    public function testConstructorWillSetStartedState(): void
    {
        $this->assertSame(QuizState::STARTED, $this->baseInstance->getState());
    }

    public function testAddQuestion(): void
    {
        $this->baseInstance->addQuestion($question1 = $this->createMock(QuizQuestion::class));
        $this->baseInstance->addQuestion($question1);
        $this->baseInstance->addQuestion($question2 = $this->createMock(QuizQuestion::class));

        $this->assertCount(2, $this->baseInstance->getQuestions());
        $this->assertContains($question1, $this->baseInstance->getQuestions());
        $this->assertContains($question2, $this->baseInstance->getQuestions());
    }

    public function testFinishWillSetFINISHEDState(): void
    {
        $this->stateStartedInstance->finish(new \DateTimeImmutable());

        $this->assertSame(QuizState::FINISHED, $this->stateStartedInstance->getState());
    }

    public function testFinishWillSetFinishedAt(): void
    {
        $this->stateStartedInstance->finish($finishedAt = new \DateTimeImmutable('+5days'));

        $this->assertSame($finishedAt, $this->stateStartedInstance->getFinishedAt());
    }

    public function testFinishWillThrowCannotChangeQuizStateExceptionAfterMakeFinished(): void
    {
        $this->expectException(CannotChangeQuizStateException::class);

        $this->stateFinishedInstance->finish(new \DateTimeImmutable('+5days'));
    }

    public function testFinishWontChangeFinishedAtIfCalledTwice(): void
    {
        $beforeFinishedAt = $this->stateFinishedInstance->getFinishedAt();

        try {
            $this->stateFinishedInstance->finish(new \DateTimeImmutable('+70days'));
        } catch (\Throwable) {
        }

        $this->assertSame($beforeFinishedAt, $this->stateFinishedInstance->getFinishedAt());
    }

    public function testGetMaximumFinishDate(): void
    {
        $instance = $this->createInstance(
            configuration: $configuration = $this->createMock(QuizConfiguration::class),
            startedAt: new \DateTimeImmutable('10:00:00 01.12.2025')
        );
        $configuration->method('getDuration')->willReturn(70);

        $this->assertEquals('11:10:00 01.12.2025', $instance->getMaximumFinishDate()->format('H:i:s d.m.Y'));
    }

    private function createInstance(
        ?QuizId $id = null,
        ?QuizConfiguration $configuration = null,
        \DateTimeImmutable $startedAt = new \DateTimeImmutable(),
    ): Quiz {
        if (null === $configuration) {
            $configuration = $this->createMock(QuizConfiguration::class);
            $configuration->method('getDuration')->willReturn(60);
            $configuration->method('getPassingScore')->willReturn(75);
        }

        return new Quiz(
            $id ?? QuizId::create(),
            $configuration,
            $startedAt
        );
    }
}
