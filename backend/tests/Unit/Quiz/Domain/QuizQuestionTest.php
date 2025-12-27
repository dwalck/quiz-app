<?php

namespace App\Tests\Unit\Quiz\Domain;

use App\Quiz\Domain\Question;
use App\Quiz\Domain\QuestionAnswer;
use App\Quiz\Domain\Quiz;
use App\Quiz\Domain\QuizQuestion;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

#[CoversClass(QuizQuestion::class)]
final class QuizQuestionTest extends TestCase
{
    public function test_constructor_set_valid_values(): void
    {
        $instance = $this->createInstance(
            $id = Uuid::v4(),
            $quiz = $this->createMock(Quiz::class),
            $question = $this->createMock(Question::class),
        );

        $this->assertSame($id, $instance->getId());
        $this->assertSame($quiz, $instance->getQuiz());
        $this->assertSame($question, $instance->getQuestion());
    }

    public function test_constructor_set_valid_answer_values(): void
    {
        $instance = $this->createInstance();

        $this->assertNull($instance->getAnswer());
        $this->assertNull($instance->getAnsweredAt());
        $this->assertFalse($instance->isAnswerCorrect());
    }

    public function test_answer_will_set_valid_answer(): void
    {
        $instance = $this->createInstance();
        $instance->answer(
            $answer = $this->createAnswerMock(false),
            new \DateTimeImmutable('now')
        );

        $this->assertSame($answer, $instance->getAnswer());
    }

    #[DataProvider('booleanDataProvider')]
    public function test_isAnswerCorrect_will_return_valid_result(bool $isAnswerCorrect): void
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

    public function test_answer_will_set_valid_answeredAt(): void
    {
        $instance = $this->createInstance();
        $instance->answer(
            $this->createAnswerMock(false),
            $date = new \DateTimeImmutable('now')
        );

        $this->assertSame($date, $instance->getAnsweredAt());
    }


    private function createInstance(
        ?Uuid $id = null,
        ?Quiz $quiz = null,
        ?Question $question = null,
    ): QuizQuestion
    {
        return new QuizQuestion(
            $id ?? Uuid::v4(),
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
