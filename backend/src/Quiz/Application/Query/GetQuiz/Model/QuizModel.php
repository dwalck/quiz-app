<?php

declare(strict_types=1);

namespace App\Quiz\Application\Query\GetQuiz\Model;

use App\Quiz\Domain\ValueObject\QuizId;
use Webmozart\Assert\Assert;

final readonly class QuizModel
{
    public string $id;

    /**
     * @param array<QuizQuestionModel> $questions
     */
    public function __construct(
        QuizId $id,
        public QuizConfigurationModel $configuration,
        public array $questions,
    ) {
        $this->id = (string) $id->getValue();

        Assert::allIsInstanceOf($this->questions, QuizQuestionModel::class);
    }
}
