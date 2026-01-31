<?php

declare(strict_types=1);

namespace App\Tests\Unit\SharedKernel\Infrastructure\Symfony;

use App\SharedKernel\Infrastructure\Symfony\Model\ViolationsList\Response\ViolationsListResponseModel;
use App\SharedKernel\Infrastructure\Symfony\ViolationsListResponseCreator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\ConstraintViolationListInterface;

/**
 * @internal
 */
#[CoversClass(ViolationsListResponseCreator::class)]
final class ViolationsListResponseCreatorTest extends TestCase
{
    public function testItCreatesValidViolations(): void
    {
        $instance = $this->createInstance();

        $model = $instance->create($this->createConstraintViolationList([
            $this->createViolation('email', 'Invalid email address.'),
            $this->createViolation('password', 'Password is too short.'),
        ]));

        $this->assertCreateModelViolations([
            $this->createAssertViolation('email', 'Invalid email address.'),
            $this->createAssertViolation('password', 'Password is too short.'),
        ], $model);
    }

    private function assertCreateModelViolations(array $expectedViolations, ViolationsListResponseModel $model): void
    {
        foreach ($expectedViolations as $expectedViolation) {
            $found = false;

            foreach ($model->violations as $violation) {
                if (
                    $violation->message === $expectedViolation->message
                    && $violation->path === $expectedViolation->propertyPath
                ) {
                    $found = true;
                    break;
                }
            }

            $this->assertTrue(
                $found,
                \sprintf(
                    'Violation not found(message="%s", path="%s").',
                    $expectedViolation->message,
                    $expectedViolation->propertyPath
                )
            );
        }
    }

    private function createAssertViolation(string $propertyPath, string $message): object
    {
        return (object) [
            'propertyPath' => $propertyPath,
            'message' => $message,
        ];
    }

    private function createInstance(): ViolationsListResponseCreator
    {
        return new ViolationsListResponseCreator();
    }

    private function createConstraintViolationList(array $violationsList): ConstraintViolationListInterface
    {
        return new ConstraintViolationList($violationsList);
    }

    private function createViolation(string $propertyPath, string $message): ConstraintViolationInterface
    {
        return new ConstraintViolation(
            $message,
            null,
            [],
            null,
            $propertyPath,
            null
        );
    }
}
