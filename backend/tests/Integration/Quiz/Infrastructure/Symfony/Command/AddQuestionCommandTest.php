<?php

declare(strict_types=1);

namespace App\Tests\Integration\Quiz\Infrastructure\Symfony\Command;

use App\Quiz\Domain\Question;
use App\Quiz\Infrastructure\Symfony\Command\AddQuestionCommand;
use App\SharedKernel\Application\ClockInterface;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Uid\Uuid;

/**
 * @internal
 */
#[CoversClass(AddQuestionCommand::class)]
final class AddQuestionCommandTest extends KernelTestCase
{
    private Command $command;

    private CommandTester $commandTester;

    private int $clockNowCalls = 0;

    protected function setUp(): void
    {
        self::bootKernel();
        $application = new Application(self::$kernel);

        $this->command = $application->find('quiz:question:add');
        $this->commandTester = new CommandTester($this->command);

        self::getContainer()->set(ClockInterface::class, $clock = $this->createMock(ClockInterface::class));
        $clock->method('now')->willReturnCallback(function () {
            $datetime = new \DateTimeImmutable('10:00:00 04.01.2025')
                ->modify(\sprintf('+%d seconds', $this->clockNowCalls))
            ;

            ++$this->clockNowCalls;

            return $datetime;
        });
    }

    public function testExecuteWillCreateQuestion(): void
    {
        $this->runCommand(
            $content = 'What color is a lemon?'.Uuid::v4()->toString(),
            [
                'Orange',
                'Yellow',
                'Red',
            ],
            '1'
        );

        $question = $this->getEntityManager()->getRepository(Question::class)->findOneBy(['content' => $content]);

        $this->assertEquals($content, $question->getContent());
        $this->assertEquals('10:00:00 04.01.2025', $question->getCreatedAt()->format('H:i:s d.m.Y'));
    }

    #[DataProvider('getCorrectAnswerValues')]
    public function testExecuteWillCreateQuestionWithAnswers(string $correctAnswer): void
    {
        $this->runCommand(
            $content = 'What color is a lemon?'.Uuid::v4()->toString(),
            [
                'Orange',
                'Yellow',
                'Red',
            ],
            $correctAnswer
        );

        $question = $this->getEntityManager()->getRepository(Question::class)->findOneBy(['content' => $content]);

        $this->assertEquals(
            [
                $this->createAnswerDataToCompare('Orange', false, '10:00:01 04.01.2025'),
                $this->createAnswerDataToCompare('Yellow', true, '10:00:02 04.01.2025'),
                $this->createAnswerDataToCompare('Red', false, '10:00:03 04.01.2025'),
            ],
            $this->getAnswersCompareData($question)
        );
    }

    public static function getCorrectAnswerValues(): array
    {
        return [
            'Index' => ['1'],
            'Content' => ['Yellow'],
        ];
    }

    #[DataProvider('getMultipleCorrectAnswerValues')]
    public function testExecuteWillCreateQuestionWithMultipleCorrectAnswers(string $correctAnswers): void
    {
        $this->runCommand(
            $content = 'What color is a lemon?'.Uuid::v4()->toString(),
            [
                'Orange',
                'Yellow',
                'Red',
            ],
            $correctAnswers
        );

        $question = $this->getEntityManager()->getRepository(Question::class)->findOneBy(['content' => $content]);

        $this->assertEquals(
            [
                $this->createAnswerDataToCompare('Orange', false, '10:00:01 04.01.2025'),
                $this->createAnswerDataToCompare('Yellow', true, '10:00:02 04.01.2025'),
                $this->createAnswerDataToCompare('Red', true, '10:00:03 04.01.2025'),
            ],
            $this->getAnswersCompareData($question)
        );
    }

    public static function getMultipleCorrectAnswerValues(): array
    {
        return [
            'Indexes separated by comma' => ['1,2'],
            'Indexes separated by comma and space' => ['1, 2'],
            'Contents separated by comma' => ['Yellow,Red'],
            'Contents separated by comma and space' => ['Yellow, Red'],
        ];
    }

    private function getAnswersCompareData(Question $question): array
    {
        $questionAnswers = [];
        foreach ($question->getAnswers() as $answer) {
            $questionAnswers[$answer->getCreatedAt()->getTimestamp()][] = $this->createAnswerDataToCompare(
                $answer->getContent(),
                $answer->isCorrect(),
                $answer->getCreatedAt()->format('H:i:s d.m.Y'),
            );
        }

        \ksort($questionAnswers);

        $sortedAnswers = [];
        foreach ($questionAnswers as $answers) {
            foreach ($answers as $answer) {
                $sortedAnswers[] = $answer;
            }
        }

        return $sortedAnswers;
    }

    private function createAnswerDataToCompare(string $content, bool $correct, string $createdAt): array
    {
        return [
            'content' => $content,
            'correct' => $correct,
            'createdAt' => $createdAt,
        ];
    }

    private function runCommand(
        string $content,
        array $answers,
        array|string|int $correctAnswersIndexes,
    ): void {
        $inputs = [$content];

        foreach ($answers as $answer) {
            $inputs[] = $answer;
        }

        $inputs[] = '';

        if (!\is_array($correctAnswersIndexes)) {
            $correctAnswersIndexes = [$correctAnswersIndexes];
        }

        foreach ($correctAnswersIndexes as $correctAnswerIndex) {
            $inputs[] = $correctAnswerIndex;
        }

        $this->commandTester->setInputs($inputs);
        $this->commandTester->execute(['command' => $this->command->getName()]);
    }

    private function getEntityManager(): EntityManagerInterface
    {
        return self::getContainer()->get(EntityManagerInterface::class);
    }
}
