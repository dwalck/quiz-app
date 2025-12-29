<?php

declare(strict_types=1);

namespace App\Tests\Unit\Quiz\Domain;

use App\Quiz\Domain\Question;
use App\Quiz\Domain\QuestionAnswer;
use App\Quiz\Domain\ValueObject\QuestionId;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(Question::class)]
final class QuestionTest extends TestCase
{
    public function testConstruct(): void
    {
        $question = new Question(
            $id = QuestionId::create(),
            $content = 'Hello world!',
            $createdAt = new \DateTimeImmutable(),
        );

        $this->assertSame($id, $question->getId());
        $this->assertEquals($content, $question->getContent());
        $this->assertSame($createdAt, $question->getCreatedAt());
        $this->assertCount(0, $question->getAnswers());
    }

    public function testAddAnswer(): void
    {
        $question = $this->createInstance();

        $question->addAnswer($answer1 = $this->createMock(QuestionAnswer::class));
        $question->addAnswer($answer1);
        $question->addAnswer($answer2 = $this->createMock(QuestionAnswer::class));

        $this->assertCount(2, $question->getAnswers());
        $this->assertContains($answer1, $question->getAnswers());
        $this->assertContains($answer2, $question->getAnswers());
    }

    private function createInstance(): Question
    {
        return new Question(QuestionId::create(), 'Hello world!', new \DateTimeImmutable());
    }
}
