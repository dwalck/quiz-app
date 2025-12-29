<?php

declare(strict_types=1);

namespace App\Tests\Unit\Quiz\Domain;

use App\Quiz\Domain\Enum\QuizState;
use App\Quiz\Domain\Exception\CannotChangeQuizStateException;
use App\Quiz\Domain\Quiz;
use App\Quiz\Domain\QuizConfiguration;
use App\Quiz\Domain\QuizQuestion;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

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
        $this->stateStartedInstance->makeStarted(new \DateTimeImmutable('+5 days'));

        $this->stateFinishedInstance = $this->createInstance();
        $this->stateFinishedInstance->makeStarted(new \DateTimeImmutable('+5 days'));
        $this->stateFinishedInstance->makeFinished(new \DateTimeImmutable('+7 days'));
    }

    public function testConstructor(): void
    {
        $instance = $this->createInstance(
            $id = Uuid::v4(),
            $configuration = $this->createMock(QuizConfiguration::class),
            $createdAt = new \DateTimeImmutable(),
        );

        $this->assertSame($id, $instance->getId());
        $this->assertSame($configuration, $instance->getConfiguration());
        $this->assertSame($createdAt, $instance->getCreatedAt());
        $this->assertEmpty($instance->getQuestions());
    }

    public function testConstructorWillSetCREATEDState(): void
    {
        $this->assertSame(QuizState::CREATED, $this->baseInstance->getState());
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

    public function testMakeStartedWillSetSTARTEDState(): void
    {
        $this->baseInstance->makeStarted(new \DateTimeImmutable());

        $this->assertSame(QuizState::STARTED, $this->baseInstance->getState());
    }

    public function testMakeStartedWillStartedAt(): void
    {
        $this->baseInstance->makeStarted($startAt = new \DateTimeImmutable('+5days'));

        $this->assertSame($startAt, $this->baseInstance->getStartedAt());
    }

    public function testMakeStartedWillThrowCannotChangeQuizStateExceptionAfterCalledMakeStartedBefore(): void
    {
        $this->baseInstance->makeStarted(new \DateTimeImmutable('+5days'));

        $this->expectException(CannotChangeQuizStateException::class);

        $this->baseInstance->makeStarted(new \DateTimeImmutable('+6days'));
    }

    public function testMakeStartedWontUpdateMakeStartedAfterCalledMakeStartedBefore(): void
    {
        $beforeStartedAt = $this->stateStartedInstance->getStartedAt();

        try {
            $this->stateStartedInstance->makeStarted(new \DateTimeImmutable('+60days'));
        } catch (\Throwable) {
        }

        $this->assertSame($beforeStartedAt, $this->stateStartedInstance->getStartedAt());
    }

    public function testMakeStartedWillThrowCannotChangeQuizStateExceptionOnFINISHEDState(): void
    {
        $this->expectException(CannotChangeQuizStateException::class);

        $this->stateFinishedInstance->makeStarted(new \DateTimeImmutable('+50days'));
    }

    public function testMakeFinishedWillSetFINISHEDState(): void
    {
        $this->stateStartedInstance->makeFinished(new \DateTimeImmutable());

        $this->assertSame(QuizState::FINISHED, $this->stateStartedInstance->getState());
    }

    public function testMakeFinishedWillSetFinishedAt(): void
    {
        $this->stateStartedInstance->makeFinished($finishedAt = new \DateTimeImmutable('+5days'));

        $this->assertSame($finishedAt, $this->stateStartedInstance->getFinishedAt());
    }

    public function testMakeFinishedWillThrowCannotChangeQuizStateExceptionAfterConstructor(): void
    {
        $this->expectException(CannotChangeQuizStateException::class);

        $this->baseInstance->makeFinished(new \DateTimeImmutable('+5days'));
    }

    public function testMakeFinishedWillThrowCannotChangeQuizStateExceptionAfterMakeFinished(): void
    {
        $this->expectException(CannotChangeQuizStateException::class);

        $this->stateFinishedInstance->makeFinished(new \DateTimeImmutable('+5days'));
    }

    public function testMakeFinishedWontChangeFinishedAtIfCalledTwice(): void
    {
        $beforeFinishedAt = $this->stateFinishedInstance->getFinishedAt();

        try {
            $this->stateFinishedInstance->makeFinished(new \DateTimeImmutable('+70days'));
        } catch (\Throwable) {
        }

        $this->assertSame($beforeFinishedAt, $this->stateFinishedInstance->getFinishedAt());
    }

    public function testGetMaximumFinishDate(): void
    {
        $instance = $this->createInstance(
            configuration: $configuration = $this->createMock(QuizConfiguration::class)
        );
        $configuration->method('getDuration')->willReturn(70);

        $instance->makeStarted(new \DateTimeImmutable('10:00:00 01.12.2025'));

        $this->assertEquals('11:10:00 01.12.2025', $instance->getMaximumFinishDate()->format('H:i:s d.m.Y'));
    }

    public function testGetMaximumFinishDateWillThrowExceptionIfOnStatusCreated(): void
    {
        $this->expectException(\DomainException::class);

        $this->baseInstance->getMaximumFinishDate();
    }

    private function createInstance(
        ?Uuid $id = null,
        ?QuizConfiguration $configuration = null,
        \DateTimeImmutable $createdAt = new \DateTimeImmutable(),
    ): Quiz {
        if (null === $configuration) {
            $configuration = $this->createMock(QuizConfiguration::class);
            $configuration->method('getDuration')->willReturn(60);
            $configuration->method('getPassingScore')->willReturn(75);
        }

        return new Quiz(
            $id ?? Uuid::v4(),
            $configuration,
            $createdAt
        );
    }
}
