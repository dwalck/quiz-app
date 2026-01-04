<?php

declare(strict_types=1);

namespace App\Quiz\Infrastructure\Symfony\Command;

use App\Quiz\Domain\QuestionAnswer;
use App\Quiz\Domain\Repository\QuestionRepositoryInterface;
use App\Quiz\Domain\ValueObject\QuestionAnswerId;
use App\Quiz\Domain\ValueObject\QuestionId;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'quiz:question:add',
    description: 'Add a question'
)]
final readonly class AddQuestionCommand
{
    public function __construct(
        private QuestionRepositoryInterface $questionRepository,
    ) {
    }

    public function __invoke(
        InputInterface $input,
        OutputInterface $output,
    ): int {
        $symfonyStyle = new SymfonyStyle($input, $output);

        $helper = new QuestionHelper();
        $question = new Question('Please enter question: ');
        $questionContent = $helper->ask($input, $output, $question);

        $answers = $this->getAnswers($symfonyStyle, $input, $output);
        $validAnswersIndexes = $this->getCorrectAnswersIndexes($answers, $input, $output);

        $questionEntity = new \App\Quiz\Domain\Question(
            QuestionId::create(),
            $questionContent,
            new \DateTimeImmutable(),
        );
        foreach ($answers as $i => $answer) {
            $questionEntity->addAnswer(new QuestionAnswer(
                QuestionAnswerId::create(),
                $questionEntity,
                $answer,
                \in_array($i, $validAnswersIndexes),
                new \DateTimeImmutable(),
            ));
        }
        $this->questionRepository->save($questionEntity);

        return Command::SUCCESS;
    }

    /**
     * @return array<string>
     */
    private function getAnswers(SymfonyStyle $symfonyStyle, InputInterface $input, OutputInterface $output): array
    {
        $symfonyStyle->info('Give question answers (leave empty and press ENTER to stop adding answers).');

        $helper = new QuestionHelper();

        $answers = [];

        $i = 0;
        while (true) {
            ++$i;
            $question = new Question(\sprintf('Enter answer #%d: ', $i));

            $lastAnswer = $helper->ask($input, $output, $question);

            if (\in_array($lastAnswer, ['', null], true)) {
                break;
            }

            $answers[] = $lastAnswer;
        }

        return $answers;
    }

    /**
     * @param array<string> $answers
     *
     * @return array<int>
     */
    private function getCorrectAnswersIndexes(array $answers, InputInterface $input, OutputInterface $output): array
    {
        $helper = new QuestionHelper();
        $question = new ChoiceQuestion(
            'Please select correct answers (separate with commas).',
            $answers,
        );
        $question->setMultiselect(true);

        return $helper->ask($input, $output, $question);
    }
}
