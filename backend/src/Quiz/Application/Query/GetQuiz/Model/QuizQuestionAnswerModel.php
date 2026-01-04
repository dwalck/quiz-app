<?php

declare(strict_types=1);

namespace App\Quiz\Application\Query\GetQuiz\Model;

use App\Quiz\Domain\ValueObject\QuestionAnswerId;

final readonly class QuizQuestionAnswerModel
{
    public function __construct(
        public QuestionAnswerId $id,
        public string $content,
    ) {
    }
}
