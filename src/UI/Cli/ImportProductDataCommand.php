<?php

declare(strict_types=1);

namespace App\UI\Cli;

use App\Entity\ProductData;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class ImportProductDataCommand extends AbstractImportCommand
{
    protected static $defaultName = 'import:products';

    public function __construct(
        readonly EntityManagerInterface $entityManager,
        readonly LoggerInterface $logger,
    ) {
        parent::__construct();
    }

    protected function processFile(string $csvPath): void
    {
        if (!is_readable($csvPath)) {
            $this->io->error('The file is not readable: ' . $csvPath);
            $this->logger->error('File is not readable', ['filePath' => $csvPath]);
            return;
        }

        $this->io->section('Processing products.csv');
        $file = fopen($csvPath, 'r');

        if (!$file) {
            $this->io->error('Failed to open the file.');
            $this->logger->error("Failed to open file: $csvPath");
            return;
        }

        $header = fgetcsv($file);
        if ($header === false || $header !== ['Product Code', 'Product Name', 'Product Description', 'Stock', 'Cost in GBP', 'Discontinued']) {
            $this->io->error('Invalid file format. Expected headers: Product Code, Product Name, Product Description, Stock, Cost in GBP, Discontinued.');
            $this->logger->error('Invalid file format', ['expected' => $header, 'filePath' => $csvPath]);
            fclose($file);
            return;
        }

        $rowCount = 0;
        $successCount = 0;
        $errorCount = 0;
        $errorMessages = [];

        while (($row = fgetcsv($file)) !== false) {
            if (count($row) !== count($header)) {
                $errorCount++;
                $errorMessages[] = "Row {$rowCount} has incorrect column count. Expected: " . count($header);
                $this->logger->warning('Invalid row format', ['row' => $row]);
                continue;
            }

            [$code, $name, $description, $stock, $cost, $discontinued] = $row;

            if ($this->isInvalidProduct($cost, $stock, $discontinued, $code)) {
                $errorCount++;
                $errorMessages[] = "Row {$rowCount}: Product with code {$code} does not meet import criteria.";
                continue;
            }

            try {
                $product = new ProductData(
                    strProductName: $name,
                    strProductDesc: $description,
                    strProductCode: $code,
                    stock: (int) $stock,
                    costInGBP: (float) $cost,
                    dtmAdded: new \DateTime(),
                    dtmDiscontinued: $discontinued === 'yes' ? new \DateTime() : null
                );

                $this->entityManager->persist($product);
                $successCount++;
            } catch (\Exception $e) {
                $errorCount++;
                $errorMessages[] = "Row {$rowCount}: Failed to insert product with code {$code} - " . $e->getMessage();
                $this->logger->error('Failed to process product', [
                    'code' => $code,
                    'exception' => $e,
                    'row' => $row
                ]);
            }

            $rowCount++;
        }

        fclose($file);

        try {
            $this->entityManager->flush();
        } catch (\Exception $e) {
            $this->io->error('Failed to save data to the database: ' . $e->getMessage());
            $this->logger->error('Database flush failed', ['exception' => $e]);
            return;
        }

        // Report Summary
        $this->io->success("Import process completed. Total rows: {$rowCount}, Successful: {$successCount}, Errors: {$errorCount}.");
        if (!empty($errorMessages)) {
            $this->io->section('Errors encountered during import:');
            foreach ($errorMessages as $message) {
                $this->io->text('- ' . $message);
            }
        }
    }

    private function isInvalidProduct(string $cost, string $stock, string $discontinued, string $code): bool
    {
        if ((float)$cost < 5 || (int)$stock < 10 || (float)$cost > 1000 || $discontinued === 'yes' && (int)$stock < 10) {
            $this->logger->warning("Invalid product for code $code: $cost, $stock. Skipping.");
            return true;
        }

        return false;
    }
}
