<?php

declare(strict_types=1);

namespace App\Tests\Integration\Quiz\Infrastructure\Doctrine\Repository;

use App\Quiz\Domain\Question;
use App\Quiz\Domain\ValueObject\QuestionId;
use App\Quiz\Infrastructure\Doctrine\Repository\QuestionRepository;
use App\Tests\Fixture\QuestionFixture;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * @internal
 */
#[CoversClass(QuestionRepository::class)]
final class QuestionRepositoryTest extends KernelTestCase
{
    private readonly QuestionRepository $questionRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->questionRepository = self::getContainer()->get(QuestionRepository::class);
    }

    public function testFindByIds(): void
    {
        $questions = $this->questionRepository->findByIds($ids = [
            QuestionId::fromString(QuestionFixture::EXAMPLE1_UUID),
            QuestionId::fromString(QuestionFixture::EXAMPLE2_UUID),
            QuestionId::fromString(QuestionFixture::EXAMPLE3_UUID),
        ]);

        $questionsIds = \array_map(function (Question $question) {
            return $question->getId();
        }, $questions);

        $this->assertEquals($questionsIds, $ids);
    }

    public function testFindByIdsEmpty(): void
    {
        $this->assertEmpty($this->questionRepository->findByIds([
            QuestionId::create(),
        ]));
    }

    public function testGetAllIds(): void
    {
        $ids = \array_map(function (QuestionId $questionId) {
            return $questionId->getValue()->__toString();
        }, $this->questionRepository->getAllIds());

        $this->assertContains(QuestionFixture::EXAMPLE1_UUID, $ids);
    }
}
