<?php

declare(strict_types=1);

namespace App\Quiz\Domain\ValueObject;

use App\SharedKernel\Domain\AbstractId;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
final class QuizId extends AbstractId
{
}
