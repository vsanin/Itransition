<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'tblProductData')]
class ProductData
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(type: 'integer')]
    private ?int $intProductDataId = null;

    #[ORM\Column(type: 'string', length: 50)]
    private string $strProductName;

    #[ORM\Column(type: 'string', length: 255)]
    private string $strProductDesc;

    #[ORM\Column(type: 'string', length: 10, unique: true)]
    private string $strProductCode;

    #[ORM\Column(type: 'integer')]
    private int $stock;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private float $costInGBP;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $dtmAdded;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $dtmDiscontinued;

    #[ORM\Column(type: 'datetime', nullable: false, options: ['default' => 'CURRENT_TIMESTAMP'])]
    private \DateTime $stmTimestamp;

    public function __construct(
        string $strProductName,
        string $strProductDesc,
        string $strProductCode,
        int $stock,
        float $costInGBP,
        ?\DateTimeInterface $dtmAdded = null,
        ?\DateTimeInterface $dtmDiscontinued = null
    ) {
        $this->strProductName = $strProductName;
        $this->strProductDesc = $strProductDesc;
        $this->strProductCode = $strProductCode;
        $this->stock = $stock;
        $this->costInGBP = $costInGBP;
        $this->dtmAdded = $dtmAdded;
        $this->dtmDiscontinued = $dtmDiscontinued;
        $this->stmTimestamp = new \DateTime();
    }

    public function getIntProductDataId(): ?int
    {
        return $this->intProductDataId;
    }

    public function getStrProductName(): string
    {
        return $this->strProductName;
    }

    public function getStrProductDesc(): string
    {
        return $this->strProductDesc;
    }

    public function getStrProductCode(): string
    {
        return $this->strProductCode;
    }

    public function getStock(): int
    {
        return $this->stock;
    }

    public function getCostInGBP(): float
    {
        return $this->costInGBP;
    }

    public function getDtmAdded(): ?\DateTimeInterface
    {
        return $this->dtmAdded;
    }

    public function getDtmDiscontinued(): ?\DateTimeInterface
    {
        return $this->dtmDiscontinued;
    }

    public function getStmTimestamp(): \DateTime
    {
        return $this->stmTimestamp;
    }
}
