<?php

namespace App\Tests\Unit\Quiz\Domain;

use App\Quiz\Domain\Question;
use App\Quiz\Domain\Quiz;
use App\Quiz\Domain\QuizConfiguration;
use App\Quiz\Domain\QuizQuestion;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

#[CoversClass(QuizConfiguration::class)]
final class QuizConfigurationTest extends TestCase
{
    public function test_constructor_set_valid_values(): void
    {
        $instance = $this->createInstance(
            49,
            79
        );

        $this->assertEquals(49, $instance->getDuration());
        $this->assertEquals(79, $instance->getPassingScore());
    }


    private function createInstance(
        int $duration = 50,
        int $passingScore = 80
    ): QuizConfiguration
    {
        return new QuizConfiguration(
            $duration,
            $passingScore
        );
    }
}
