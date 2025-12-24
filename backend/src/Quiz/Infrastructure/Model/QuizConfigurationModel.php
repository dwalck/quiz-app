<?php

declare(strict_types=1);

namespace App\Quiz\Infrastructure\Model;

final readonly class QuizConfigurationModel
{
    /**
     * @param int $duration     (in minutes)
     * @param int $passingScore (as percentage)
     */
    public function __construct(
        public int $questionsCount,
        public int $duration,
        public int $passingScore,
    ) {
    }
}
