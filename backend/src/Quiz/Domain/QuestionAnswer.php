<?php

namespace App\Quiz\Domain;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
class QuestionAnswer
{
    #[
        ORM\Id,
        ORM\Column(type: UuidType::NAME)
    ]
    private readonly Uuid $id;

    #[
        ORM\ManyToOne(targetEntity: Question::class),
        ORM\JoinColumn(nullable: false)
    ]
    private readonly Question $question;

    #[ORM\Column(type: Types::STRING, length: 2000)]
    private string $content;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $correct;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private readonly \DateTimeImmutable $createdAt;

    public function __construct(
        Uuid $id,
        Question $question,
        string $content,
        bool $correct,
        \DateTimeImmutable $createdAt
    )
    {
        $this->id = $id;
        $this->question = $question;
        $this->content = $content;
        $this->correct = $correct;
        $this->createdAt = $createdAt;
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getQuestion(): Question
    {
        return $this->question;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function isCorrect(): bool
    {
        return $this->correct;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }
}
