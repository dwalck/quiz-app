<?php

namespace App\Tests\Unit\Quiz\Domain;

use App\Quiz\Domain\Enum\QuizState;
use App\Quiz\Domain\Exception\CannotChangeQuizStateException;
use App\Quiz\Domain\Question;
use App\Quiz\Domain\Quiz;
use App\Quiz\Domain\QuizConfiguration;
use App\Quiz\Domain\QuizQuestion;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

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

    public function test_constructor(): void
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

    public function test_constructor_will_set_CREATED_state(): void
    {
        $this->assertSame(QuizState::CREATED, $this->baseInstance->getState());
    }

    public function test_addQuestion(): void
    {
        $this->baseInstance->addQuestion($question1 = $this->createMock(QuizQuestion::class));
        $this->baseInstance->addQuestion($question1);
        $this->baseInstance->addQuestion($question2 = $this->createMock(QuizQuestion::class));

        $this->assertCount(2, $this->baseInstance->getQuestions());
        $this->assertContains($question1, $this->baseInstance->getQuestions());
        $this->assertContains($question2, $this->baseInstance->getQuestions());
    }

    public function test_makeStarted_will_set_STARTED_state(): void
    {
        $this->baseInstance->makeStarted(new \DateTimeImmutable());

        $this->assertSame(QuizState::STARTED, $this->baseInstance->getState());
    }

    public function test_makeStarted_will_startedAt(): void
    {
        $this->baseInstance->makeStarted($startAt = new \DateTimeImmutable('+5days'));

        $this->assertSame($startAt, $this->baseInstance->getStartedAt());
    }

    public function test_makeStarted_will_throw_CannotChangeQuizStateException_after_called_makeStarted_before(): void
    {
        $this->baseInstance->makeStarted(new \DateTimeImmutable('+5days'));

        $this->expectException(CannotChangeQuizStateException::class);

        $this->baseInstance->makeStarted(new \DateTimeImmutable('+6days'));
    }

    public function test_makeStarted_wont_update_makeStarted_after_called_makeStarted_before(): void
    {
        $beforeStartedAt = $this->stateStartedInstance->getStartedAt();

        try {
            $this->stateStartedInstance->makeStarted(new \DateTimeImmutable('+60days'));
        } catch (\Throwable) {}

        $this->assertSame($beforeStartedAt, $this->stateStartedInstance->getStartedAt());
    }

    public function test_makeStarted_will_throw_CannotChangeQuizStateException_on_FINISHED_state(): void
    {
        $this->expectException(CannotChangeQuizStateException::class);

        $this->stateFinishedInstance->makeStarted(new \DateTimeImmutable('+50days'));
    }

    public function test_makeFinished_will_set_FINISHED_state(): void
    {
        $this->stateStartedInstance->makeFinished(new \DateTimeImmutable());

        $this->assertSame(QuizState::FINISHED, $this->stateStartedInstance->getState());
    }

    public function test_makeFinished_will_set_finishedAt(): void
    {
        $this->stateStartedInstance->makeFinished($finishedAt = new \DateTimeImmutable('+5days'));

        $this->assertSame($finishedAt, $this->stateStartedInstance->getFinishedAt());
    }

    public function test_makeFinished_will_throw_CannotChangeQuizStateException_after_constructor(): void
    {
        $this->expectException(CannotChangeQuizStateException::class);

        $this->baseInstance->makeFinished(new \DateTimeImmutable('+5days'));
    }

    public function test_makeFinished_will_throw_CannotChangeQuizStateException_after_makeFinished(): void
    {
        $this->expectException(CannotChangeQuizStateException::class);

        $this->stateFinishedInstance->makeFinished(new \DateTimeImmutable('+5days'));
    }

    public function test_makeFinished_wont_change_finishedAt_if_called_twice(): void
    {
        $beforeFinishedAt = $this->stateFinishedInstance->getFinishedAt();

        try {
            $this->stateFinishedInstance->makeFinished(new \DateTimeImmutable('+70days'));
        } catch (\Throwable) {}

        $this->assertSame($beforeFinishedAt, $this->stateFinishedInstance->getFinishedAt());
    }

    public function test_getMaximumFinishDate(): void
    {
        $instance = $this->createInstance(
            configuration: $configuration = $this->createMock(QuizConfiguration::class)
        );
        $configuration->method('getDuration')->willReturn(70);

        $instance->makeStarted(new \DateTimeImmutable('10:00:00 01.12.2025'));

        $this->assertEquals('11:10:00 01.12.2025', $instance->getMaximumFinishDate()->format('H:i:s d.m.Y'));
    }

    public function test_getMaximumFinishDate_will_throw_exception_if_on_status_created(): void
    {
        $this->expectException(\DomainException::class);

        $this->baseInstance->getMaximumFinishDate();
    }


    private function createInstance(
        ?Uuid $id = null,
        ?QuizConfiguration $configuration = null,
        \DateTimeImmutable $createdAt = new \DateTimeImmutable()
    ): Quiz
    {
        if ($configuration === null) {
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
