<?php

declare(strict_types=1);

namespace App\Quiz\Domain;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
class Question
{
    #[
        ORM\Id,
        ORM\Column(type: UuidType::NAME)
    ]
    private readonly Uuid $id;

    #[ORM\Column(type: Types::STRING, length: 2000)]
    private string $content;

    #[ORM\OneToMany(targetEntity: QuestionAnswer::class, mappedBy: 'question', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $answers;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private readonly \DateTimeImmutable $createdAt;

    public function __construct(
        Uuid $id,
        string $content,
        \DateTimeImmutable $createdAt,
    ) {
        $this->id = $id;
        $this->content = $content;
        $this->createdAt = $createdAt;

        $this->answers = new ArrayCollection();
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @return array<QuestionAnswer>
     */
    public function getAnswers(): array
    {
        return $this->answers->toArray();
    }

    public function addAnswer(QuestionAnswer $answer): void
    {
        $this->answers->add($answer);
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }
}
