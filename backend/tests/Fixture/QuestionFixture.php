<?php

declare(strict_types=1);

namespace App\Tests\Fixture;

use App\Quiz\Domain\Question;
use App\Quiz\Domain\ValueObject\QuestionId;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

final class QuestionFixture extends Fixture
{
    private ObjectManager $manager;

    public const string EXAMPLE1_UUID = 'c5394b22-9cb0-45a1-92bc-81deca135c7b';
    public const string EXAMPLE2_UUID = '03e7d17f-dce3-4bb9-bb1f-addc8788b6e7';
    public const string EXAMPLE3_UUID = 'ef651299-758f-4931-b03b-11b5230f3d78';

    public function load(ObjectManager $manager): void
    {
        $this->manager = $manager;

        $this->doCreate(self::EXAMPLE1_UUID, '2 + 2?');
        $this->doCreate(self::EXAMPLE2_UUID, 'How to declare variable in PHP?');
        $this->doCreate(self::EXAMPLE3_UUID, 'Capital of Poland?');
    }

    private function doCreate(string $uuid, string $content): void
    {
        $example = new Question(
            QuestionId::fromString($uuid),
            $content,
            new \DateTimeImmutable('10:00:00 30.12.2025')
        );

        $this->manager->persist($example);
        $this->manager->flush();
    }
}
