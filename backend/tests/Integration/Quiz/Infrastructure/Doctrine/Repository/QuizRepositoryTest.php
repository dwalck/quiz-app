<?php

declare(strict_types=1);

namespace App\Tests\Integration\Quiz\Infrastructure\Doctrine\Repository;

use App\Quiz\Domain\Enum\QuizState;
use App\Quiz\Domain\Quiz;
use App\Quiz\Domain\QuizConfiguration;
use App\Quiz\Domain\ValueObject\QuizId;
use App\Quiz\Infrastructure\Doctrine\Repository\QuizRepository;
use App\SharedKernel\Domain\Exception\EntityNotFoundException;
use App\Tests\Fixture\QuizFixture;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * @internal
 */
#[CoversClass(QuizRepository::class)]
final class QuizRepositoryTest extends KernelTestCase
{
    private readonly QuizRepository $quizRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->quizRepository = self::getContainer()->get(QuizRepository::class);
    }

    public function testGet(): void
    {
        $this->assertInstanceOf(
            Quiz::class,
            $this->quizRepository->get(QuizId::fromString(QuizFixture::SAMPLE_QUIZ_UUID))
        );
    }

    public function testGetWillThrowEntityNotFoundException(): void
    {
        $this->expectException(EntityNotFoundException::class);

        $this->quizRepository->get(QuizId::create());
    }

    public function testSaveWillCreate(): void
    {
        $this->quizRepository->save(new Quiz(
            $id = QuizId::create(),
            new QuizConfiguration(
                10,
                15
            ),
            new \DateTimeImmutable()
        ));

        $this->getEntityManager()->clear();

        $this->assertInstanceOf(Quiz::class, $this->quizRepository->get($id));
    }

    public function testSaveWillUpdate(): void
    {
        $quiz = $this->quizRepository->get(QuizId::fromString(QuizFixture::SAMPLE_QUIZ_UUID));
        $quiz->makeStarted(new \DateTimeImmutable());

        $this->quizRepository->save($quiz);
        $this->getEntityManager()->clear();

        $updatedQuiz = $this->quizRepository->get(QuizId::fromString(QuizFixture::SAMPLE_QUIZ_UUID));

        $this->assertSame(QuizState::STARTED, $updatedQuiz->getState());
    }

    private function getEntityManager(): EntityManagerInterface
    {
        return self::getContainer()->get(EntityManagerInterface::class);
    }
}
