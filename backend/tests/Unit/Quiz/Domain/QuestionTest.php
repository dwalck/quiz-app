<?php

namespace App\Tests\Unit\Quiz\Domain;

use App\Quiz\Domain\Question;
use App\Quiz\Domain\QuestionAnswer;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

#[CoversClass(Question::class)]
final class QuestionTest extends TestCase
{
    public function test_construct(): void
    {
        $question = new Question(
            $id = Uuid::v4(),
            $content = 'Hello world!',
            $createdAt = new \DateTimeImmutable(),
        );

        $this->assertSame($id, $question->getId());
        $this->assertEquals($content, $question->getContent());
        $this->assertSame($createdAt, $question->getCreatedAt());
        $this->assertCount(0, $question->getAnswers());
    }

    public function test_addAnswer(): void
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
        return new Question(Uuid::v4(),'Hello world!',new \DateTimeImmutable());
    }
}
