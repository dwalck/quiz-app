<?php

declare(strict_types=1);

namespace App\Tests\Unit\Quiz\Domain;

use App\Quiz\Domain\QuizConfiguration;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(QuizConfiguration::class)]
final class QuizConfigurationTest extends TestCase
{
    public function testConstructorSetValidValues(): void
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
        int $passingScore = 80,
    ): QuizConfiguration {
        return new QuizConfiguration(
            $duration,
            $passingScore
        );
    }
}
