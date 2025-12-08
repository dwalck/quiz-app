<?php

namespace App\Quiz\Infrastructure\Model;

use Symfony\Component\Uid\Uuid;

final readonly class QuestionAnswerModel
{
    public function __construct(
        public Uuid $uuid,
        public string $content
    )
    {
    }
}
