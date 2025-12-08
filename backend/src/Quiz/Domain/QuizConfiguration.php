<?php

namespace App\Quiz\Domain;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
readonly class QuizConfiguration
{
    /**
     * In minutes
     */
    #[ORM\Column(type: Types::INTEGER)]
    private int $duration;

    /**
     * As percentage
     */
    #[ORM\Column(type: Types::INTEGER)]
    private int $passingScore;

    public function __construct(
        int $duration,
        int $passingScore
    )
    {
        $this->duration = $duration;
        $this->passingScore = $passingScore;
    }

    /**
     * Returns value in minutes
     */
    public function getDuration(): int
    {
        return $this->duration;
    }

    /**
     * Returns value as percentage
     */
    public function getPassingScore(): int
    {
        return $this->passingScore;
    }
}
