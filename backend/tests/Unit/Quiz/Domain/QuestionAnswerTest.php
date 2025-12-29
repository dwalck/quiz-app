<?php

declare(strict_types=1);

namespace App\Tests\Unit\Quiz\Domain;

use App\Quiz\Domain\Question;
use App\Quiz\Domain\QuestionAnswer;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

/**
 * @internal
 */
#[CoversClass(QuestionAnswer::class)]
final class QuestionAnswerTest extends TestCase
{
    public function testConstruct(): void
    {
        $instance = $this->createInstance(
            $id = Uuid::v4(),
            $question = $this->createMock(Question::class),
            $content = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
            false,
            $date = new \DateTimeImmutable('now'),
        );

        $this->assertSame($id, $instance->getId());
        $this->assertSame($question, $instance->getQuestion());
        $this->assertSame($content, $instance->getContent());
        $this->assertFalse($instance->isCorrect());
        $this->assertSame($date, $instance->getCreatedAt());
    }

    private function createInstance(
        ?Uuid $id = null,
        ?Question $question = null,
        string $content = 'Lorem ipsum.',
        bool $correct = true,
        \DateTimeImmutable $createdAt = new \DateTimeImmutable('now'),
    ): QuestionAnswer {
        return new QuestionAnswer(
            $id,
            $question,
            $content,
            $correct,
            $createdAt
        );
    }
}
