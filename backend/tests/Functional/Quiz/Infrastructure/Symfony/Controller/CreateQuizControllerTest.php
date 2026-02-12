<?php

declare(strict_types=1);

namespace App\Tests\Functional\Quiz\Infrastructure\Symfony\Controller;

use App\Quiz\Domain\Enum\QuizState;
use App\Quiz\Domain\Quiz;
use App\Quiz\Domain\ValueObject\QuizId;
use App\Quiz\Infrastructure\Symfony\Controller\CreateQuizController;
use App\SharedKernel\Application\ClockInterface;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * @internal
 */
#[CoversClass(CreateQuizController::class)]
final class CreateQuizControllerTest extends WebTestCase
{
    private const string PATH = '/quiz';

    protected function setUp(): void
    {
        self::createClient();

        self::getContainer()->set(ClockInterface::class, $clock = $this->createMock(ClockInterface::class));
        $clock->method('now')->willReturn(new \DateTimeImmutable('2025-01-05 12:00:00'));
    }

    public function testRequestValidDataWillCreateQuiz(): void
    {
        $this->request(self::createData(
            numberOfQuestions: 2,
            duration: 54,
            passingScore: 80
        ));

        $data = \json_decode(self::getClient()->getResponse()->getContent(), true);
        $quiz = $this->getQuiz($data['id']);

        $this->assertCount(2, $quiz->getQuestions());
        $this->assertEquals(54, $quiz->getConfiguration()->getDuration());
        $this->assertEquals(80, $quiz->getConfiguration()->getPassingScore());
        $this->assertSame(QuizState::STARTED, $quiz->getState());
        $this->assertEquals('2025-01-05 12:00:00', $quiz->getStartedAt()->format('Y-m-d H:i:s'));
    }

    public function testRequestValidDataWillCreateQuizWithNoAnsweredQuestions(): void
    {
        $this->request(self::createData(
            numberOfQuestions: 2,
            duration: 54,
            passingScore: 80
        ));

        $data = \json_decode(self::getClient()->getResponse()->getContent(), true);
        $quiz = $this->getQuiz($data['id']);

        $questions = $quiz->getQuestions();

        $nullValues = 0;
        foreach ($questions as $question) {
            if (null === $question->getAnswer()) {
                ++$nullValues;
            }
            if (null === $question->getAnsweredAt()) {
                ++$nullValues;
            }
        }

        $this->assertEquals(\count($questions) * 2, $nullValues);
    }

    #[DataProvider('getInvalidDataWithViolationPath')]
    public function testRequestInvalidDataWillRespondHttp422(array $data): void
    {
        $this->request($data);

        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    #[DataProvider('getInvalidDataWithViolationPath')]
    public function testRequestInvalidDataWillRespondContentWithViolationAthPath(array $data, string $invalidPath): void
    {
        $this->request($data);

        $this->assertResponseContainsViolationAtPath($invalidPath);
    }

    public static function getInvalidDataWithViolationPath(): array
    {
        return [
            [self::createData(numberOfQuestions: 0), 'numberOfQuestions'],
            [self::createData(numberOfQuestions: 201), 'numberOfQuestions'],
            [self::createData(duration: 0), 'duration'],
            [self::createData(duration: 601), 'duration'],
            [self::createData(passingScore: 0), 'passingScore'],
            [self::createData(passingScore: 101), 'passingScore'],
        ];
    }

    private function request(array $data): void
    {
        self::getClient()->jsonRequest('POST', self::PATH, $data);
    }

    private static function createData(
        int $numberOfQuestions = 30,
        int $duration = 60,
        int $passingScore = 75,
    ): array {
        return [
            'numberOfQuestions' => $numberOfQuestions,
            'duration' => $duration,
            'passingScore' => $passingScore,
        ];
    }

    private function assertResponseContainsViolationAtPath(string $path): void
    {
        $response = self::getClient()->getResponse();

        $data = \json_decode($response->getContent(), true);

        $violations = $data['violations'];

        $violationsPaths = [];

        foreach ($violations as $violation) {
            $violationsPaths[] = $violation['path'];
        }

        $this->assertContains($path, $violationsPaths);
    }

    private function getQuiz(string $id): Quiz
    {
        /** @var EntityManagerInterface $em */
        $em = self::getContainer()->get(EntityManagerInterface::class);

        return $em->getRepository(Quiz::class)->find(QuizId::fromString($id)->getValue());
    }
}
