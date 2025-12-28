<?php

declare(strict_types=1);

namespace App\Quiz\Infrastructure\Symfony\Command;

use App\Quiz\Application\Command\CreateQuiz\CreateQuizCommand;
use App\Quiz\Domain\Repository\QuizRepositoryInterface;
use App\SharedKernel\Infrastructure\Symfony\CommandDispatcher;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Attribute\Option;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Uid\Uuid;

#[AsCommand(name: 'quiz:start', description: 'Start a quiz!')]
final readonly class QuizCommand
{
    public function __construct(
        private CommandDispatcher $commandDispatcher,
        private QuizRepositoryInterface $quizRepository,
    ) {
    }

    public function __invoke(
        SymfonyStyle $io,
        #[Option] int $questionsCount = 100,
        #[Option] int $duration = 60,
        #[Option] int $passingScore = 70,
    ): int {
        $this->commandDispatcher->dispatch(new CreateQuizCommand(
            $id = Uuid::v4(),
            $questionsCount,
            $duration,
            $passingScore
        ));

        $quiz = $this->quizRepository->get($id);

        dump($quiz);

        foreach ($quiz->getQuestions() as $question) {
        }

        return Command::SUCCESS;
    }
}
