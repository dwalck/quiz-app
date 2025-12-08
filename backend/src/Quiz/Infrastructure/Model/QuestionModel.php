<?php

namespace App\Quiz\Infrastructure\Model;

use Symfony\Component\Uid\Uuid;
use Webmozart\Assert\Assert;

final readonly class QuestionModel
{
    /**
     * @param array<QuestionAnswerModel> $answers
     */
    public function __construct(
        public Uuid $uuid,
        public string $content,
        public array $answers
    )
    {
        Assert::allIsInstanceOf($this->answers, QuestionAnswerModel::class);
    }
}
