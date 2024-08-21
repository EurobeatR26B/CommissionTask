<?php

declare (strict_types=1);

use Justas\CommissionTask\FileInput\CsvReader;
use Justas\CommissionTask\Operation\Operation;
use Justas\CommissionTask\Operation\OperationParser;
use Justas\CommissionTask\Operation\OperationRepository;
use Justas\CommissionTask\Operation\OperationType;
use Justas\CommissionTask\User\UserType;
use PHPUnit\Framework\TestCase;

class OperationParserTest extends TestCase
{
    private OperationParser $parser;

    protected function setUp(): void
    {
        $this->parser = new OperationParser();    
    }

    public function testParseLine()
    {
        $line = [
            'date'          => '2024-08-20',
            'userID'        => '1',
            'userType'      => 'private',
            'operationType' => 'withdraw',
            'amount'        => '1.00',
            'currency'      => 'EUR',
        ];

        $line = (object) $line;

        $operationLine = $this->parser->parseSingleLine($line);

        $operationObject = new Operation(
            new DateTime('2024-08-20'),
            1,
            UserType::PRIVATE,
            OperationType::WITHDRAW,
            1.00,
            'EUR'
        );

        $this->assertSame($operationObject::class, $operationLine::class);
        $this->assertEquals($operationObject->getDate(), $operationLine->getDate());
        $this->assertSame($operationObject->getUserID(), $operationLine->getUserID());
        $this->assertSame($operationObject->getUserType(), $operationLine->getUserType());
        $this->assertSame($operationObject->getOperationType(), $operationLine->getOperationType());
        $this->assertSame($operationObject->getAmount(), $operationLine->getAmount());
        $this->assertSame($operationObject->getCurrency(), $operationLine->getCurrency());

    }
}