<?php

declare(strict_types=1);

namespace App\Quiz\Application\Service\QuestionSelection;

use App\Quiz\Domain\Question;
use App\Quiz\Domain\Repository\QuestionRepositoryInterface;

readonly class QuizSelectionService implements QuizSelectionServiceInterface
{
    public function __construct(
        private QuestionRepositoryInterface $questionRepository,
    ) {
    }

    /**
     * @return array<Question>
     */
    public function select(int $count): array
    {
        $allIds = $this->questionRepository->getAllIds();

        $existingCount = \min($count, \count($allIds));

        $keys = \array_rand($allIds, $existingCount);
        if (!\is_array($keys)) {
            // This condition will execute when there is only one question and array_rand returns a single element as an int|string
            $keys = [$keys];
        }

        $ids = [];
        foreach ($keys as $key) {
            $ids[] = $allIds[$key];
        }

        return $this->questionRepository->findByIds($ids);
    }
}
