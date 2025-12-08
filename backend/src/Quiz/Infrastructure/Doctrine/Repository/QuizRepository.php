<?php

namespace App\Quiz\Infrastructure\Doctrine\Repository;

use App\Quiz\Domain\Quiz;
use App\Quiz\Domain\Repository\QuizRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

final readonly class QuizRepository implements QuizRepositoryInterface
{
    private EntityRepository $repository;

    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
        $this->repository = $entityManager->getRepository(Quiz::class);
    }

    public function save(Quiz $quiz): void
    {
        $this->entityManager->persist($quiz);
        $this->entityManager->flush();
    }
}
