<?php

declare(strict_types=1);

use Justas\CommissionTask\Operation\Operation;
use Justas\CommissionTask\Operation\OperationRepository;
use Justas\CommissionTask\Operation\OperationType;
use Justas\CommissionTask\User\UserType;
use PHPUnit\Framework\TestCase;

class OperationRepositoryTest extends TestCase
{
    private OperationRepository $repository;

    protected function setUp(): void
    {
        $this->repository = new OperationRepository();
    }

    public function testAddOperation()
    {
        $operation = new Operation(
            new DateTime('2024-08-20'),
            1,
            UserType::PRIVATE,
            OperationType::DEPOSIT,
            1.00,
            'EUR'
        );

        $this->repository->addOperation($operation);

        $operations = $this->repository->getAll();

        $this->assertSame(
            [
            $operation->getUserID() => [$operation]
        ],
            $operations
        );
    }

    public function testGetSingleOperationByUser()
    {
        $operation = new Operation(
            new DateTime('2024-08-20'),
            1,
            UserType::PRIVATE,
            OperationType::DEPOSIT,
            1.00,
            'EUR'
        );

        $this->repository->addOperation($operation);

        $retrievedOperation = $this->repository->getOperationsByUser(1)[0];

        $this->assertSame($operation, $retrievedOperation);
    }

    public function testGetMultipleOperationsByUser()
    {
        $operation = new Operation(
            new DateTime('2024-08-20'),
            1,
            UserType::PRIVATE,
            OperationType::DEPOSIT,
            1.00,
            'EUR'
        );

        $this->repository->addOperation($operation);
        $this->repository->addOperation($operation);
        $this->repository->addOperation($operation);

        $retrievedOperationCount = count($this->repository->getOperationsByUser(1));

        $this->assertSame(3, $retrievedOperationCount);
    }

    public function testGetOperationsByMultipleUsers()
    {
        $operation = new Operation(
            new DateTime('2024-08-20'),
            1,
            UserType::PRIVATE,
            OperationType::DEPOSIT,
            1.00,
            'EUR'
        );

        $operation2 = new Operation(
            new DateTime('2024-08-20'),
            2,
            UserType::PRIVATE,
            OperationType::DEPOSIT,
            1.00,
            'EUR'
        );

        $this->repository->addOperation($operation);
        $this->repository->addOperation($operation2);

        $userOperations = $this->repository->getOperationsByUser(1);
        $userOperations2 = $this->repository->getOperationsByUser(2);

        $this->assertSame([$operation], $userOperations);

        $this->assertSame([$operation2], $userOperations2);

        $this->assertSame(1, count($userOperations));
        $this->assertSame(1, count($userOperations2));
    }

    public function testGetOperationsByUserAndPeriod()
    {
        $operation = new Operation(
            new DateTime('2024-08-20'),
            1,
            UserType::PRIVATE,
            OperationType::DEPOSIT,
            1.00,
            'EUR'
        );

        $operation2 = new Operation(
            new DateTime('2024-08-01'),
            1,
            UserType::PRIVATE,
            OperationType::DEPOSIT,
            1.00,
            'EUR'
        );

        $operation3 = new Operation(
            new DateTime('2024-08-01'),
            1,
            UserType::PRIVATE,
            OperationType::DEPOSIT,
            1.00,
            'EUR'
        );

        $operation4 = new Operation(
            new DateTime('2024-08-01'),
            1,
            UserType::PRIVATE,
            OperationType::DEPOSIT,
            1.00,
            'EUR'
        );

        $this->repository->addOperation($operation);
        $this->repository->addOperation($operation2);
        $this->repository->addOperation($operation3);
        $this->repository->addOperation($operation4);

        $userOperations = $this->repository->getOperationsByUserAndPeriod($operation->getUserID(), $operation->getPeriodOfOperation());
        $userOperations2 = $this->repository->getOperationsByUserAndPeriod($operation2->getUserID(), $operation2->getPeriodOfOperation());

        $this->assertSame(1, count($userOperations));
        $this->assertSame(3, count($userOperations2));
    }


}
