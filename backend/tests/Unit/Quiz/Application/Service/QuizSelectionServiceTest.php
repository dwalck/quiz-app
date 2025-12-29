<?php

declare(strict_types=1);

namespace App\Tests\Unit\Quiz\Application\Service;

use App\Quiz\Application\Service\QuestionSelection\QuizSelectionService;
use App\Quiz\Domain\Question;
use App\Quiz\Domain\Repository\QuestionRepositoryInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

/**
 * @internal
 */
#[CoversClass(QuizSelectionService::class)]
final class QuizSelectionServiceTest extends TestCase
{
    private QuestionRepositoryInterface $questionRepository;

    private QuizSelectionService $quizSelectionService;

    protected function setUp(): void
    {
        $this->questionRepository = $this->createMock(QuestionRepositoryInterface::class);

        $this->quizSelectionService = new QuizSelectionService($this->questionRepository);
    }

    public function testSelectWontCallRepositoryFindByIdsIfRepositoryGetAllIdsReturnsEmptyArray(): void
    {
        $this->questionRepository->method('getAllIds')->willReturn([]);

        $this->questionRepository->expects($this->never())->method('findByIds');

        $this->quizSelectionService->select(100);
    }

    public function testSelectWillCallFindByIdsWithValidArguments(): void
    {
        $ids = [
            Uuid::v4(),
            Uuid::v4(),
            Uuid::v4(),
        ];

        $this->questionRepository->method('getAllIds')->willReturn($ids);

        $this->questionRepository
            ->expects($this->once())
            ->method('findByIds')
            ->with($this->equalTo($ids))
        ;

        $this->quizSelectionService->select(100);
    }

    public function testSelectWillCallFindByIdsWithValidArgumentsIfSingleIdFound(): void
    {
        $ids = [
            Uuid::v4(),
        ];

        $this->questionRepository->method('getAllIds')->willReturn($ids);

        $this->questionRepository
            ->expects($this->once())
            ->method('findByIds')
            ->with($this->equalTo($ids))
        ;

        $this->quizSelectionService->select(100);
    }

    public function testSelectWillReturnRepositoryFindByIdsResult(): void
    {
        $this->questionRepository->method('getAllIds')->willReturn([
            Uuid::v4(),
            Uuid::v4(),
        ]);

        $this->questionRepository->method('findByIds')->willReturn($questions = [
            $this->createStub(Question::class),
            $this->createStub(Question::class),
        ]);

        $this->assertEquals($questions, $this->quizSelectionService->select(100));
    }
}
