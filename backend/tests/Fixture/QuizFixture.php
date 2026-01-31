<?php

declare(strict_types=1);

namespace App\Tests\Fixture;

use App\Quiz\Domain\Quiz;
use App\Quiz\Domain\QuizConfiguration;
use App\Quiz\Domain\ValueObject\QuizId;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

final class QuizFixture extends Fixture
{
    public const string QUIZ_STATE_CREATED_UUID = '2db12bf4-8f4a-4825-bb5d-33271309f6fa';

    public function load(ObjectManager $manager): void
    {
        $quiz = new Quiz(
            QuizId::fromString(self::QUIZ_STATE_CREATED_UUID),
            new QuizConfiguration(
                10,
                15
            ),
            new \DateTimeImmutable('10:00:00 29.12.2025')
        );

        $manager->persist($quiz);
        $manager->flush();
    }
}
