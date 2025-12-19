<?php

namespace App\Quiz\Application\GetQuestions;

use App\Quiz\Infrastructure\Doctrine\Repository\QuestionRepository;

readonly class GetQuestionsHandler
{
    public function __construct(
        private QuestionRepository $questionRepository
    )
    {
    }

    public function query(GetQuestionsQuery $query): array
    {
        $allIds = $this->questionRepository->getAllIds();

        $count = min($query->questionsCount, count($allIds));

        $keys = array_rand($allIds, $count);

        $ids = [];
        foreach ($keys as $key) {
            $ids[] = $allIds[$key];
        }

        return $this->questionRepository->findByIds($ids);
    }
}
