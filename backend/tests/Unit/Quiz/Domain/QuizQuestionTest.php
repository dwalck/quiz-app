<?php

declare(strict_types=1);

namespace App\Tests\Unit\Quiz\Domain;

use App\Quiz\Domain\Question;
use App\Quiz\Domain\QuestionAnswer;
use App\Quiz\Domain\Quiz;
use App\Quiz\Domain\QuizQuestion;
use App\Quiz\Domain\ValueObject\QuizQuestionId;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(QuizQuestion::class)]
final class QuizQuestionTest extends TestCase
{
    public function testConstructorSetValidValues(): void
    {
        $instance = $this->createInstance(
            $id = QuizQuestionId::create(),
            $quiz = $this->createMock(Quiz::class),
            $question = $this->createMock(Question::class),
        );

        $this->assertSame($id, $instance->getId());
        $this->assertSame($quiz, $instance->getQuiz());
        $this->assertSame($question, $instance->getQuestion());
    }

    public function testConstructorSetValidAnswerValues(): void
    {
        $instance = $this->createInstance();

        $this->assertNull($instance->getAnswer());
        $this->assertNull($instance->getAnsweredAt());
        $this->assertFalse($instance->isAnswerCorrect());
    }

    public function testAnswerWillSetValidAnswer(): void
    {
        $instance = $this->createInstance();
        $instance->answer(
            $answer = $this->createAnswerMock(false),
            new \DateTimeImmutable('now')
        );

        $this->assertSame($answer, $instance->getAnswer());
    }

    #[DataProvider('booleanDataProvider')]
    public function testIsAnswerCorrectWillReturnValidResult(bool $isAnswerCorrect): void
    {
        $instance = $this->createInstance();

        $instance->answer(
            $this->createAnswerMock($isAnswerCorrect),
            new \DateTimeImmutable('now')
        );

        $this->assertEquals($isAnswerCorrect, $instance->isAnswerCorrect());
    }

    public static function booleanDataProvider(): iterable
    {
        return [
            'true' => [true],
            'false' => [false],
        ];
    }

    public function testAnswerWillSetValidAnsweredAt(): void
    {
        $instance = $this->createInstance();
        $instance->answer(
            $this->createAnswerMock(false),
            $date = new \DateTimeImmutable('now')
        );

        $this->assertSame($date, $instance->getAnsweredAt());
    }

    private function createInstance(
        ?QuizQuestionId $id = null,
        ?Quiz $quiz = null,
        ?Question $question = null,
    ): QuizQuestion {
        return new QuizQuestion(
            $id ?? QuizQuestionId::create(),
            $quiz ?? $this->createMock(Quiz::class),
            $question ?? $this->createMock(Question::class),
        );
    }

    private function createAnswerMock(bool $isCorrect = true): QuestionAnswer
    {
        $answer = $this->createMock(QuestionAnswer::class);
        $answer->method('isCorrect')->willReturn($isCorrect);

        return $answer;
    }
}
