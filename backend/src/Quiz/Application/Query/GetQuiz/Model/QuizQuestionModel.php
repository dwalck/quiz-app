<?php

declare(strict_types=1);

namespace App\Quiz\Application\Query\GetQuiz\Model;

use App\Quiz\Domain\ValueObject\QuizQuestionId;
use Webmozart\Assert\Assert;

final readonly class QuizQuestionModel
{
    /**
     * @param array<QuizQuestionAnswerModel> $answers
     */
    public function __construct(
        public QuizQuestionId $id,
        public string $content,
        public array $answers,
    ) {
        Assert::allIsInstanceOf($this->answers, QuizQuestionAnswerModel::class);
    }
}
