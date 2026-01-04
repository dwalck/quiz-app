<?php

declare(strict_types=1);

namespace App\Quiz\Application\Query\GetQuiz\Model;

use App\Quiz\Domain\ValueObject\QuizId;
use Webmozart\Assert\Assert;

final readonly class QuizModel
{
    /**
     * @param array<QuizQuestionModel> $questions
     */
    public function __construct(
        public QuizId $id,
        public QuizConfigurationModel $configuration,
        public array $questions,
    ) {
        Assert::allIsInstanceOf($this->questions, QuizQuestionModel::class);
    }
}
