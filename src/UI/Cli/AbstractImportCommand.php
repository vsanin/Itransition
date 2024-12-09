<?php

declare(strict_types=1);

namespace App\UI\Cli;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidOptionException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

abstract class AbstractImportCommand extends Command
{
    protected const ARGUMENT_CSV_PATH = 'csv-path';

    protected SymfonyStyle $io;

    protected function configure(): void
    {
        $this->addArgument(self::ARGUMENT_CSV_PATH, InputArgument::OPTIONAL, 'Path to csv');
    }

    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->io = new SymfonyStyle($input, $output);
    }

    protected function interact(InputInterface $input, OutputInterface $output): void
    {
        $csvPath = $input->getArgument(self::ARGUMENT_CSV_PATH);

        if (null === $csvPath)
        {
            $this->io->title('csv path');
            $this->io->text([
                'What is the path to csv?',
            ]);

            $csvPath = $this->io->ask('csv-path', null, $this->validateIfPathIsCorrect(...));
            $input->setArgument(self::ARGUMENT_CSV_PATH, $csvPath);
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $csvPath = $input->getArgument(self::ARGUMENT_CSV_PATH);

        $this->processFile($csvPath);

        return Command::SUCCESS;
    }

    protected function validateIfPathIsCorrect(string $path): string
    {
        if (!file_exists($path))
        {
            throw new InvalidOptionException('Path to file is incorrect');
        }

        return $path;
    }

    abstract protected function processFile(string $csvPath): void;
}
