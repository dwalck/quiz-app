<?php

declare(strict_types=1);

namespace App\Quiz\Application\Query\GetQuiz\Model;

final readonly class QuizConfigurationModel
{
    /**
     * @param int $duration     (in minutes)
     * @param int $passingScore (as percentage)
     */
    public function __construct(
        public int $duration,
        public int $passingScore,
    ) {
    }
}
