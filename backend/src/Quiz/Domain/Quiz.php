<?php

declare(strict_types=1);

namespace App\Quiz\Domain;

use App\Quiz\Domain\Enum\QuizState;
use App\Quiz\Domain\Exception\CannotChangeQuizStateException;
use App\Quiz\Domain\ValueObject\QuizId;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
class Quiz
{
    #[ORM\Embedded(columnPrefix: false)]
    private readonly QuizId $id;

    /**
     * @var Collection<int, QuizStateChange>
     */
    #[ORM\OneToMany(targetEntity: QuizStateChange::class, mappedBy: 'quiz', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $stateChanges;

    #[ORM\Embedded(columnPrefix: 'configuration_')]
    private readonly QuizConfiguration $configuration;

    /**
     * @var Collection<int, QuizQuestion>
     */
    #[ORM\OneToMany(targetEntity: QuizQuestion::class, mappedBy: 'quiz', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $questions;

    public function __construct(
        QuizId $id,
        QuizConfiguration $configuration,
        \DateTimeImmutable $createdAt,
    ) {
        $this->id = $id;
        $this->configuration = $configuration;

        $this->stateChanges = new ArrayCollection();
        $this->questions = new ArrayCollection();

        $this->addStatusChange(QuizState::CREATED, $createdAt);
    }

    public function getId(): QuizId
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

        if (null === $stateChange) {
            throw new \DomainException('Quiz does not have any state changes.');
        }

        return $stateChange->getState();
    }

    public function makeStarted(\DateTimeImmutable $startedAt): void
    {
        if (($state = $this->getState()) !== QuizState::CREATED) {
            throw new CannotChangeQuizStateException($state, QuizState::STARTED);
        }

        $this->addStatusChange(QuizState::STARTED, $startedAt);
    }

    public function makeFinished(\DateTimeImmutable $finishedAt): void
    {
        if (($state = $this->getState()) !== QuizState::STARTED) {
            throw new CannotChangeQuizStateException($state, QuizState::FINISHED);
        }

        $this->addStatusChange(QuizState::FINISHED, $finishedAt);
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->getStatusChange(QuizState::CREATED)->getChangedAt();
    }

    public function getStartedAt(): \DateTimeImmutable
    {
        return $this->getStatusChange(QuizState::STARTED)->getChangedAt();
    }

    public function getFinishedAt(): \DateTimeImmutable
    {
        return $this->getStatusChange(QuizState::FINISHED)->getChangedAt();
    }

    public function getMaximumFinishDate(): \DateTimeImmutable
    {
        return $this->getStatusChange(QuizState::STARTED)->getChangedAt()->modify(\sprintf('+%d minutes', $this->configuration->getDuration()));
    }

    private function getStatusChange(QuizState $state): QuizStateChange
    {
        foreach ($this->stateChanges->toArray() as $change) {
            if ($change->getState() === $state) {
                return $change;
            }
        }

        throw new \DomainException(\sprintf('Quiz does not have state change for state "%s".', $state->name));
    }

    private function addStatusChange(QuizState $state, \DateTimeImmutable $at): void
    {
        $this->stateChanges->add(new QuizStateChange(Uuid::v4(), $this, $state, $at));
    }
}
