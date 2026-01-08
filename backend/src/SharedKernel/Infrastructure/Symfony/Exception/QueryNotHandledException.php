<?php

declare(strict_types=1);

namespace App\SharedKernel\Infrastructure\Symfony\Exception;

final class QueryNotHandledException extends \RuntimeException
{
    public function __construct(object $query)
    {
        parent::__construct(\sprintf(
            'Query "%s" not handled.',
            $query::class
        ));
    }
}
