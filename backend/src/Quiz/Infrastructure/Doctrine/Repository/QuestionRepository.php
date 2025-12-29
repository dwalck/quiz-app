<?php

declare(strict_types=1);

namespace App\Quiz\Infrastructure\Doctrine\Repository;

use App\Quiz\Domain\Question;
use App\Quiz\Domain\Repository\QuestionRepositoryInterface;
use App\Quiz\Domain\ValueObject\QuestionId;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Webmozart\Assert\Assert;

final readonly class QuestionRepository implements QuestionRepositoryInterface
{
    /**
     * @var EntityRepository<Question>
     */
    private EntityRepository $repository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->repository = $entityManager->getRepository(Question::class);
    }

    /**
     * @return array<Question>
     */
    public function findByIds(array $ids): array
    {
        Assert::allIsInstanceOf($ids, QuestionId::class);

        return $this->repository->findBy(['id' => \array_map(function (QuestionId $id) {
            return $id->getValue();
        }, $ids)]);
    }

    /**
     * @return array<QuestionId>
     */
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
