<?php

namespace App\Tests\Unit\Quiz\Application\QuizCreator;

use App\Quiz\Application\QuizCreator\QuizCreator;
use App\Quiz\Domain\Enum\QuizState;
use App\Quiz\Domain\Event\QuizCreatedEvent;
use App\Quiz\Domain\Question;
use App\Quiz\Domain\Quiz;
use App\Quiz\Domain\QuizQuestion;
use App\Quiz\Domain\Repository\QuizRepositoryInterface;
use App\SharedKernel\Application\ClockInterface;
use App\SharedKernel\Application\EventDispatcherInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

#[CoversClass(QuizCreator::class)]
final class QuizCreatorTest extends TestCase
{
    private EventDispatcherInterface&MockObject $eventDispatcher;

    private QuizRepositoryInterface&MockObject $quizRepository;

    private ClockInterface&MockObject $clock;

    /**
     * @var array|(Question&MockObject[])
     */
    private array $questions;
    private Question $question1;
    private Question $question2;
    private Question $question3;

    private \DateTimeImmutable $now;

    public function setUp(): void
    {
        $this->quizRepository = $this->createMock(QuizRepositoryInterface::class);
        $this->eventDispatcher = $this->createMock(EventDispatcherInterface::class);

        $this->clock = $this->createMock(ClockInterface::class);
        $this->clock->method('now')->willReturn($this->now = new \DateTimeImmutable('2025-12-30 11:30:45'));

        $this->questions = [
            $this->question1 = $this->createQuestion(),
            $this->question2 = $this->createQuestion(),
            $this->question3 = $this->createQuestion(),
        ];
    }

    #[DataProvider('getInvalidValues')]
    public function test_create_will_throw_InvalidArgumentException_if_questions_contain_invalid_values(mixed $questions): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->callCreate($questions);
    }

    public static function getInvalidValues(): array
    {
        return [
            'String' => [['test']],
            'Int' => [[1]],
            'True' => [[true]],
            'False' => [[false]],
            'Array' => [[[]]],
            'stdClass' => [[new \stdClass()]],
        ];
    }

    public function test_create_will_throw_InvalidArgumentException_if_questions_empty(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->callCreate([]);
    }

    public function test_create_will_throw_exception_with_correct_message(): void
    {
        $this->expectExceptionMessage('Quiz must contain at least one question.');

        $this->callCreate([]);
    }

    public function test_create_will_return_quiz_with_valid_createdAt(): void
    {
        $quiz = $this->callCreate();

        $this->assertSame($this->now, $quiz->getCreatedAt());
    }

    public function test_create_will_return_quiz_with_valid_state(): void
    {
        $quiz = $this->callCreate();

        $this->assertSame(QuizState::CREATED, $quiz->getState());
    }

    public function test_create_will_return_quiz_with_valid_configuration(): void
    {
        $quiz = $this->callCreate(duration: 55, passingScore: 92);

        $this->assertEquals(55, $quiz->getConfiguration()->getDuration());
        $this->assertEquals(92, $quiz->getConfiguration()->getPassingScore());
    }

    public function test_create_will_return_quiz_with_questions(): void
    {
        $quiz = $this->callCreate([
            $this->question1,
            $this->question3,
        ]);

        $questions = array_map(function (QuizQuestion $question) {
            return $question->getQuestion();
        }, $quiz->getQuestions());

        $this->assertContains($this->question1, $questions);
        $this->assertNotContains($this->question2, $questions);
        $this->assertContains($this->question3, $questions);
    }

    public function test_create_will_dispatch_QuizCreatedEvent(): void
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

        $quiz = $this->callCreate();

        $this->assertSame($quizFromEvent, $quiz);
    }

    public function test_create_will_call_save_on_quizRepository(): void
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

        $quiz = $this->callCreate();

        $this->assertSame($quizFromSaveRepository, $quiz);
    }

    private function callCreate(?array $questions = null, int $duration = 60, int $passingScore = 70): Quiz
    {
        return $this->createInstance()->create($questions ?? $this->questions, $duration, $passingScore);
    }

    private function createQuestion(): Question&MockObject
    {
        return $this->createMock(Question::class);
    }

    private function createInstance(): QuizCreator
    {
        return new QuizCreator(
            $this->quizRepository,
            $this->eventDispatcher,
            $this->clock,
        );
    }
}
