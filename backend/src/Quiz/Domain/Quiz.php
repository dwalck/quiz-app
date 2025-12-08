<?php

namespace App\Quiz\Domain;

use App\Quiz\Domain\Enum\QuizState;
use App\Quiz\Domain\Exception\CannotChangeQuizStateException;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
class Quiz
{
    #[
        ORM\Id,
        ORM\Column(type: UuidType::NAME)
    ]
    private readonly Uuid $id;

    #[ORM\ManyToOne(targetEntity: QuizStateChange::class, inversedBy: 'quiz')]
    private Collection $stateChanges;

    #[ORM\Embedded(columnPrefix: 'configuration_')]
    private readonly QuizConfiguration $configuration;

    #[ORM\ManyToOne(targetEntity: QuizQuestion::class, inversedBy: 'quiz')]
    private Collection $questions;

    public function __construct(
        Uuid $id,
        QuizConfiguration $configuration,
        \DateTimeImmutable $createdAt
    )
    {
        $this->id = $id;
        $this->configuration = $configuration;

        $this->stateChanges = new ArrayCollection();
        $this->questions = new ArrayCollection();

        $this->addStatusChange(QuizState::CREATED, $createdAt);
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getConfiguration(): QuizConfiguration
    {
        return $this->configuration;
    }

    /**
     * @return array<QuizQuestion>
     */
    public function getQuestions(): array
    {
        return $this->questions->toArray();
    }

    public function addQuestion(QuizQuestion $question): void
    {
        if (!$this->questions->contains($question)) {
            $this->questions->add($question);
        }
    }

    public function getState(): QuizState
    {
        /** @var QuizStateChange $stateChange */
        $stateChange = $this->stateChanges->last();

        if ($stateChange === null) {
            throw new \DomainException('Quiz does not have any state changes.');
        }

        return $stateChange->getState();
    }

    public function makeStarted(\DateTimeImmutable $packedAt): void
    {
        if (($state = $this->getState()) !== QuizState::CREATED) {
            throw new CannotChangeQuizStateException($state, QuizState::STARTED);
        }

        $this->addStatusChange(QuizState::STARTED, $packedAt);
    }

    public function makeFinished(\DateTimeImmutable $packedAt): void
    {
        if (($state = $this->getState()) !== QuizState::STARTED) {
            throw new CannotChangeQuizStateException($state, QuizState::FINISHED);
        }

        $this->addStatusChange(QuizState::FINISHED, $packedAt);
    }

    public function getMaximumFinishDate(): \DateTimeImmutable
    {
        return $this->getStatusChange(QuizState::STARTED)->getChangedAt()->modify(sprintf('+%d minutes', $this->configuration->getDuration()));
    }

    private function getStatusChange(QuizState $state): QuizStateChange
    {
        foreach ($this->stateChanges->toArray() as $change) {
            if ($change->getState() === $state) {
                return $change;
            }
        }

        throw new \DomainException(sprintf('Quiz does not have state change for state "%s".', $state->name));
    }

    private function addStatusChange(QuizState $state, \DateTimeImmutable $at): void
    {
        $this->stateChanges->add(new QuizStateChange(Uuid::v4(), $state, $at));
    }
}
