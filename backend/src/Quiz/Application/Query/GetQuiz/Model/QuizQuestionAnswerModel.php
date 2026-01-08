<?php

declare(strict_types=1);

namespace App\Quiz\Application\Query\GetQuiz\Model;

use App\Quiz\Domain\ValueObject\QuestionAnswerId;

final readonly class QuizQuestionAnswerModel
{
    public string $id;

    public function __construct(
        QuestionAnswerId $id,
        public string $content,
    ) {
        $this->id = (string) $id->getValue();
    }
}
