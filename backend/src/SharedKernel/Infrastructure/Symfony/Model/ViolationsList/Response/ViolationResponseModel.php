<?php

declare(strict_types=1);

namespace App\SharedKernel\Infrastructure\Symfony\Model\ViolationsList\Response;

readonly class ViolationResponseModel
{
    public function __construct(
        public string $path,
        public string $message,
    ) {
    }
}
