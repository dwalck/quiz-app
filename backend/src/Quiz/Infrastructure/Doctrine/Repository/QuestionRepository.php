<?php

declare(strict_types=1);

namespace App\Quiz\Infrastructure\Doctrine\Repository;

use App\Quiz\Domain\Question;
use App\Quiz\Domain\Repository\QuestionRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Uid\Uuid;
use Webmozart\Assert\Assert;

final readonly class QuestionRepository implements QuestionRepositoryInterface
{
    private EntityRepository $repository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->repository = $entityManager->getRepository(Question::class);
    }

    public function findByIds(array $ids): array
    {
        Assert::allIsInstanceOf($ids, Uuid::class);

        return $this->repository->findAll();
    }

    public function getAllIds(): array
    {
        $results = $this->repository->createQueryBuilder('u')
            ->select('u.id')
            ->getQuery()
            ->getArrayResult()
        ;

        return \array_map(function (array $result) {
            return $result['id'];
        }, $results);
    }
}
