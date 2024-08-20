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

    public function testParseFile()
    {
        $file = fopen('testParseFile.csv', 'w');
        $data = "2014-12-31,4,private,withdraw,1200.00,EUR
        2015-01-01,4,private,withdraw,1000.00,EUR";

        fwrite($file, $data);

        $csvParser = (new CsvReader())->setFileName('testParseFile.csv');

        $operations = $this->parser->parseFile($csvParser);

        $this->assertSame(OperationRepository::class, $operations::class);

        $operationArray = 
        [
            4 => [
                new Operation(
                    new DateTime('2014-12-31'),
                    4,
                    UserType::PRIVATE,
                    OperationType::WITHDRAW,
                    1200.00,
                    'EUR'
                ),
                new Operation(
                    new DateTime('2015-01-01'),
                    4,
                    UserType::PRIVATE,
                    OperationType::WITHDRAW,
                    1000.00,
                    'EUR'
                )        
                ]    
            ];

        $this->assertEquals($operationArray, $operations->getAll());

        fclose($file);
        unlink('testParseFile.csv');
    }
}