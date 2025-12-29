<?php

declare(strict_types=1);

namespace App\Tests\Unit\Quiz\Application\Command\CreateQuiz;

use App\Quiz\Application\Command\CreateQuiz\CreateQuizCommand;
use App\Quiz\Application\Command\CreateQuiz\CreateQuizHandler;
use App\Quiz\Application\Service\QuestionSelection\QuizSelectionServiceInterface;
use App\Quiz\Domain\Enum\QuizState;
use App\Quiz\Domain\Event\QuizCreatedEvent;
use App\Quiz\Domain\Question;
use App\Quiz\Domain\Quiz;
use App\Quiz\Domain\QuizQuestion;
use App\Quiz\Domain\Repository\QuizRepositoryInterface;
use App\SharedKernel\Application\ClockInterface;
use App\SharedKernel\Application\EventDispatcherInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(CreateQuizHandler::class)]
final class CreateQuizHandlerTest extends TestCase
{
    private EventDispatcherInterface&MockObject $eventDispatcher;

    private QuizRepositoryInterface&MockObject $quizRepository;

    private ClockInterface&MockObject $clock;

    private QuizSelectionServiceInterface $quizSelectionService;

    private Question $question1;
    private Question $question2;
    private Question $question3;

    private \DateTimeImmutable $now;

    public function setUp(): void
    {
        $this->quizRepository = $this->createMock(QuizRepositoryInterface::class);
        $this->eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $this->quizSelectionService = $this->createMock(QuizSelectionServiceInterface::class);

        $this->clock = $this->createMock(ClockInterface::class);
        $this->clock->method('now')->willReturn($this->now = new \DateTimeImmutable('2025-12-30 11:30:45'));

        $this->question1 = $this->createQuestion();
        $this->question2 = $this->createQuestion();
        $this->question3 = $this->createQuestion();
    }

    public function testCreateWillReturnQuizWithValidCreatedAt(): void
    {
        $quiz = $this->callInvoke();

        $this->assertSame($this->now, $quiz->getCreatedAt());
    }

    public function testCreateWillReturnQuizWithValidState(): void
    {
        $quiz = $this->callInvoke();

        $this->assertSame(QuizState::CREATED, $quiz->getState());
    }

    public function testCreateWillReturnQuizWithValidConfiguration(): void
    {
        $quiz = $this->callInvoke(duration: 55, passingScore: 92);

        $this->assertEquals(55, $quiz->getConfiguration()->getDuration());
        $this->assertEquals(92, $quiz->getConfiguration()->getPassingScore());
    }

    public function testCreateWillReturnQuizWithQuestions(): void
    {
        $this->quizSelectionService
            ->method('select')
            ->with(7)
            ->willReturn([
                $this->question1,
                $this->question3,
            ])
        ;

        $quiz = $this->callInvoke(questionsCount: 7);

        $questions = \array_map(function (QuizQuestion $question) {
            return $question->getQuestion();
        }, $quiz->getQuestions());

        $this->assertContains($this->question1, $questions);
        $this->assertNotContains($this->question2, $questions);
        $this->assertContains($this->question3, $questions);
    }

    public function testCreateWillDispatchQuizCreatedEvent(): void
    {
        $quizFromEvent = null;

        $this->eventDispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->with($this->callback(function (QuizCreatedEvent $event) use (&$quizFromEvent) {
                $quizFromEvent = $event->quiz;

                return true;
            }))
        ;

        $quiz = $this->callInvoke();

        $this->assertSame($quizFromEvent, $quiz);
    }

    public function testCreateWillCallSaveOnQuizRepository(): void
    {
        $quizFromSaveRepository = null;

        $this->quizRepository
            ->expects($this->once())
            ->method('save')
            ->with($this->callback(function (Quiz $quiz) use (&$quizFromSaveRepository) {
                $quizFromSaveRepository = $quiz;

                return true;
            }))
        ;

        $quiz = $this->callInvoke();

        $this->assertSame($quizFromSaveRepository, $quiz);
    }

    private function callInvoke(int $questionsCount = 5, int $duration = 60, int $passingScore = 70): Quiz
    {
        return $this->createInstance()->__invoke(new CreateQuizCommand(
            $questionsCount,
            $duration,
            $passingScore
        ));
    }

    private function createQuestion(): Question&MockObject
    {
        return $this->createMock(Question::class);
    }

    private function createInstance(): CreateQuizHandler
    {
        return new CreateQuizHandler(
            $this->quizRepository,
            $this->eventDispatcher,
            $this->clock,
            $this->quizSelectionService
        );
    }
}
