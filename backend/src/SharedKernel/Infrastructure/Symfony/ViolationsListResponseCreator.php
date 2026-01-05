<?php

declare(strict_types=1);

namespace App\SharedKernel\Infrastructure\Symfony;

use App\SharedKernel\Infrastructure\Symfony\Model\ViolationsList\Response\ViolationResponseModel;
use App\SharedKernel\Infrastructure\Symfony\Model\ViolationsList\Response\ViolationsListResponseModel;
use Symfony\Component\Validator\ConstraintViolationListInterface;

readonly class ViolationsListResponseCreator
{
    public function create(ConstraintViolationListInterface $constraintViolationList): ViolationsListResponseModel
    {
        $violations = [];

        foreach ($constraintViolationList as $constraintViolation) {
            $violations[] = new ViolationResponseModel(
                $constraintViolation->getPropertyPath(),
                (string) $constraintViolation->getMessage(),
            );
        }

        return new ViolationsListResponseModel($violations);
    }
}
