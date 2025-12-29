<?php

declare(strict_types=1);

namespace App\Quiz\Infrastructure\Doctrine\Repository;

use App\Quiz\Domain\Quiz;
use App\Quiz\Domain\Repository\QuizRepositoryInterface;
use App\Quiz\Domain\ValueObject\QuizId;
use App\SharedKernel\Domain\Exception\EntityNotFoundException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

final readonly class QuizRepository implements QuizRepositoryInterface
{
    /**
     * @var EntityRepository<Quiz>
     */
    private EntityRepository $repository;

    public function __construct(private EntityManagerInterface $entityManager)
    {
        $this->repository = $entityManager->getRepository(Quiz::class);
    }

    public function get(QuizId $id): Quiz
    {
        return $this->repository->find($id->getValue())
            ?? throw EntityNotFoundException::forSingleField(Quiz::class, 'id', $id->getValue());
    }

    public function save(Quiz $quiz): void
    {
        $this->entityManager->persist($quiz);
        $this->entityManager->flush();
    }
}
