<?php

declare(strict_types=1);

namespace App\Tests\UI\Cli;

use App\UI\Cli\ImportProductDataCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class ImportProductDataCommandTest extends TestCase
{
    private ImportProductDataCommand $command;
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $logger = $this->createMock(LoggerInterface::class);

        $this->command = new ImportProductDataCommand($this->entityManager, $logger);
    }

    public function testProcessFileWithValidCsv(): void
    {
        $input = new ArrayInput([
            'csv-path' => __DIR__ . '/stock.csv'
        ]);
        $output = new BufferedOutput();

        $result = $this->command->run($input, $output);

        $this->assertEquals(0, $result);
        $this->assertStringContainsString('Import process completed.', $output->fetch());
    }

    public function testProcessFileWithInvalidCsv(): void
    {
        $input = new ArrayInput([
            'csv-path' => __DIR__ . '/invalid_stock.csv'
        ]);
        $output = new BufferedOutput();

        $result = $this->command->run($input, $output);

        $this->assertEquals(0, $result);
        $this->assertStringContainsString('Invalid file format.', $output->fetch());
    }

    public function testProcessFileWithUnreadableFile(): void
    {
        $input = new ArrayInput([
            'csv-path' => '/path/to/unreadable_file.csv'
        ]);
        $output = new BufferedOutput();

        $result = $this->command->run($input, $output);

        $this->assertEquals(0, $result);
        $this->assertStringContainsString('The file is not readable', $output->fetch());
    }

    public function testProcessFileWithExceptionDuringFlush(): void
    {
        $input = new ArrayInput([
            'csv-path' => __DIR__ . '/stock.csv'
        ]);
        $output = new BufferedOutput();

        $this->entityManager->expects($this->once())
            ->method('flush')
            ->will($this->throwException(new \Exception('Database flush failed')));

        $result = $this->command->run($input, $output);

        $this->assertEquals(0, $result);
        $this->assertStringContainsString('Failed to save data to the database', $output->fetch());
    }
}
