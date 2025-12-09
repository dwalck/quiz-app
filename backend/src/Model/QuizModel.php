<?php

namespace App\Model;

use Symfony\Component\Uid\Uuid;

final readonly class QuizModel
{
    public function __construct(
        public Uuid $uuid,
    )
    {
    }
}
