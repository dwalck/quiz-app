<?php

declare(strict_types=1);

namespace App\Tests\Functional\Quiz\Infrastructure\Symfony\Controller;

use App\Quiz\Infrastructure\Symfony\Controller\GetQuizController;
use App\Tests\Fixture\QuizFixture;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Uid\Uuid;

/**
 * @internal
 */
#[CoversClass(GetQuizController::class)]
final class GetQuizControllerTest extends WebTestCase
{
    private const string PATH = '/quiz/{uuid}';

    private const string UUID = QuizFixture::QUIZ_STATE_CREATED_UUID;

    protected function setUp(): void
    {
        self::createClient();
    }

    public function testRespondSuccess(): void
    {
        $this->doRequest(self::UUID);

        $this->assertResponseIsSuccessful();
    }

    public function testNotExistRespondNotFound(): void
    {
        $this->doRequest(Uuid::v4()->toString());

        $this->assertResponseStatusCodeSame(404);
    }

    public function testNotExistRespondEmptyJson(): void
    {
        $this->doRequest(Uuid::v4()->toString());

        $this->assertEquals('{}', self::getClient()->getResponse()->getContent());
    }

    private function doRequest(string $uuid): void
    {
        self::getClient()->request('GET', \strtr(self::PATH, ['{uuid}' => $uuid]));
    }
}
