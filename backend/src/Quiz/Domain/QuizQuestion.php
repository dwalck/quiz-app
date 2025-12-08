<?php

namespace App\Quiz\Domain;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
class QuizQuestion
{
    #[
        ORM\Id,
        ORM\Column(type: UuidType::NAME)
    ]
    private readonly Uuid $id;

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
        Uuid $id,
        Quiz $quiz,
        Question $question
    )
    {
        $this->id = $id;
        $this->quiz = $quiz;
        $this->question = $question;

        $this->answer = null;
        $this->answeredAt = null;
    }

    public function getId(): Uuid
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
