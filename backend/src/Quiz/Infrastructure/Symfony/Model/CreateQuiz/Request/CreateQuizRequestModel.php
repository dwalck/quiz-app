<?php

declare(strict_types=1);

namespace App\Quiz\Infrastructure\Symfony\Model\CreateQuiz\Request;

use Symfony\Component\Validator\Constraints as Assert;

final class CreateQuizRequestModel
{
    #[Assert\Range(min: 1, max: 200)]
    public int $numberOfQuestions = 0;

    #[Assert\Range(min: 1, max: 600)]
    public int $duration = 0;

    #[Assert\Range(min: 1, max: 100)]
    public int $passingScore = 0;
}
