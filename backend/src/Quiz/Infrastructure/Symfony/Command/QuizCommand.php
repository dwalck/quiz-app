<?php

declare(strict_types=1);

namespace App\Quiz\Infrastructure\Symfony\Command;

use App\Quiz\Application\QuizCreator\QuizCreatorInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Attribute\Option;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'quiz:start', description: 'Start a quiz!')]
final readonly class QuizCommand
{
    public function __construct(
        private QuizCreatorInterface $quizCreator
    )
    {
    }

    public function __invoke(
        SymfonyStyle $io,
        #[Option] int $questionsCount = 100,
        #[Option] int $duration = 60,
        #[Option] int $passingScore = 70
    ): int
    {
        $quiz = $this->quizCreator->create($questionsCount, $duration, $passingScore);

        foreach ($quiz->getQuestions() as $question) {
            $io->
        }

        return Command::SUCCESS;
    }
}
