<?php

declare(strict_types=1);

namespace App\Quiz\Domain;

use App\Quiz\Domain\ValueObject\QuizQuestionId;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class QuizQuestion
{
    #[ORM\Embedded(columnPrefix: false)]
    private readonly QuizQuestionId $id;

    #[ORM\ManyToOne(targetEntity: Quiz::class)]
    #[ORM\JoinColumn(nullable: false)]
    private readonly Quiz $quiz;

    #[ORM\ManyToOne(targetEntity: Question::class)]
    #[ORM\JoinColumn(nullable: false)]
    private readonly Question $question;

    #[ORM\ManyToOne(targetEntity: QuestionAnswer::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?QuestionAnswer $answer;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $answeredAt;

    public function __construct(
        QuizQuestionId $id,
        Quiz $quiz,
        Question $question,
    ) {
        $this->id = $id;
        $this->quiz = $quiz;
        $this->question = $question;

        $this->answer = null;
        $this->answeredAt = null;
    }

    public function getId(): QuizQuestionId
    {
        return $this->id;
    }

    public function getQuiz(): Quiz
    {
        return $this->quiz;
    }

    public function getQuestion(): Question
    {
        return $this->question;
    }

    public function getAnswer(): ?QuestionAnswer
    {
        return $this->answer;
    }

    public function getAnsweredAt(): ?\DateTimeImmutable
    {
        return $this->answeredAt;
    }

    public function answer(QuestionAnswer $answer, \DateTimeImmutable $answeredAt): void
    {
        $this->answer = $answer;
        $this->answeredAt = $answeredAt;
    }

    public function isAnswerCorrect(): bool
    {
        if (null === $this->answer) {
            return false;
        }

        return $this->answer->isCorrect();
    }
}
