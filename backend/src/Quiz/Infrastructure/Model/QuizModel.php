<?php

namespace App\Quiz\Infrastructure\Model;

use Symfony\Component\Uid\Uuid;
use Webmozart\Assert\Assert;

final readonly class QuizModel
{
    /**
     * @param array<QuestionModel> $questions
     */
    public function __construct(
        public Uuid $uuid,
        public QuizConfigurationModel $configuration,
        public array $questions,
    )
    {
        Assert::allIsInstanceOf($this->questions, QuestionModel::class);
    }
}
