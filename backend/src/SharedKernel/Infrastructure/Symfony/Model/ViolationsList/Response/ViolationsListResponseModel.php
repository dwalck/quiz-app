<?php

declare(strict_types=1);

namespace App\SharedKernel\Infrastructure\Symfony\Model\ViolationsList\Response;

use Webmozart\Assert\Assert;

readonly class ViolationsListResponseModel
{
    /**
     * @param array<ViolationResponseModel> $violations
     */
    public function __construct(
        public array $violations,
    ) {
        Assert::allIsInstanceOf($this->violations, ViolationResponseModel::class);
    }
}
