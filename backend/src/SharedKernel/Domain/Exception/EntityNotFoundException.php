<?php

declare(strict_types=1);

namespace App\SharedKernel\Domain\Exception;

final class EntityNotFoundException extends \DomainException
{
    public static function forSingleField(
        string $entityClass,
        string $field,
        string|int|bool|float|\Stringable|null $value = null,
    ): self {
        if ($value instanceof \Stringable) {
            $value = $value->__toString();
        }

        return new self(\sprintf(
            'Entity "%s" with "%s"="%s" not found.',
            $entityClass,
            $field,
            $value
        ));
    }
}
