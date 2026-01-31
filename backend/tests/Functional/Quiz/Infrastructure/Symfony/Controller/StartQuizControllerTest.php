<?php

declare(strict_types=1);

namespace App\Tests\Functional\Quiz\Infrastructure\Symfony\Controller;

use App\Quiz\Domain\Enum\QuizState;
use App\Quiz\Domain\Quiz;
use App\Quiz\Infrastructure\Symfony\Controller\StartQuizController;
use App\Tests\Fixture\QuizFixture;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Clock\Test\ClockSensitiveTrait;

/**
 * @internal
 */
#[CoversClass(StartQuizController::class)]
final class StartQuizControllerTest extends WebTestCase
{
    use ClockSensitiveTrait;

    private const string PATH = '/quiz/{uuid}/start';

    private const string CREATED_UUID = QuizFixture::QUIZ_STATE_CREATED_UUID;

    protected function setUp(): void
    {
        self::createClient();

        self::mockTime('2026-01-31 10:00:00');
    }

    public function testItRespondHttpOk(): void
    {
        $this->doRequest();

        $this->assertResponseIsSuccessful();
    }

    public function testItStartQuiz(): void
    {
        $quizState = $this->getQuiz()->getState();
        if (QuizState::CREATED !== $quizState) {
            throw new \Exception(\sprintf('Test requires the quiz to have a status of "%s", but it has "%s".', QuizState::CREATED->value, $quizState->value));
        }

        $this->doRequest();

        $quiz = $this->getQuiz();

        $this->assertSame(QuizState::STARTED, $quiz->getState());
        $this->assertSame('2026-01-31 10:00:00', $quiz->getStartedAt()->format('Y-m-d H:i:s'));
    }

    public function testItRespondHttp400IfQuizAlreadyStarted(): void
    {
        $quizState = $this->getQuiz()->getState();
        if (QuizState::CREATED !== $quizState) {
            throw new \Exception(\sprintf('Test requires the quiz to have a status of "%s", but it has "%s".', QuizState::CREATED->value, $quizState->value));
        }

        $this->doRequest();
        $this->doRequest();

        $this->assertResponseStatusCodeSame(400);
    }

    private function doRequest(string $uuid = self::CREATED_UUID): void
    {
        self::getClient()->request('POST', \strtr(self::PATH, [
            '{uuid}' => $uuid,
        ]));
    }

    private function getQuiz(string $uuid = self::CREATED_UUID): Quiz
    {
        /** @var EntityManagerInterface $em */
        $em = self::getContainer()->get(EntityManagerInterface::class);
        $em->clear();

        return $em->getRepository(Quiz::class)->find($uuid);
    }
}
