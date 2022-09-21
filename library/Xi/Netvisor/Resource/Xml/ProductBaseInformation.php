<?php

namespace Xi\Netvisor\Resource\Xml;

use Xi\Netvisor\Resource\Xml\Component\AttributeElement;

class ProductBaseInformation
{
    public const BATCH_PROCESSING_NONE = 1;
    public const BATCH_PROCESSING_MANUAL = 2;
    public const BATCH_PROCESSING_DELIVERY_NEWEST = 3;
    public const BATCH_PROCESSING_DELIVERY_OLDEST = 4;
    public const BATCH_PROCESSING_USEBY_NEWEST = 5;
    public const BATCH_PROCESSING_USEBY_OLDEST = 6;
    public const BATCH_PROCESSING_MANUFACTURING_NEWEST = 7;
    public const BATCH_PROCESSING_MANUFACTURING_OLDEST = 8;

    public const UNIT_PRICE_TYPE_WITH_VAT = 'gross';
    public const UNIT_PRICE_TYPE_WITHOUT_VAT = 'net';

    public const EAN_CODE_TYPE_ANY = 'any';
    public const EAN_CODE_TYPE_EAN8 = 'ean8';
    public const EAN_CODE_TYPE_EAN13 = 'ean13';
    public const EAN_CODE_TYPE_CODE128 = 'code128';

    private $productCode;
    private $productGroup;
    private $name;
    private $description;
    private $unitPrice;
    private $unit;
    private $unitWeight;
    private $purchasePrice;
    private $tariffHeading;
    private $comissionPercentage;
    private $isActive = 1;
    private $isSalesProduct = 1;
    private $inventoryEnabled;
    private $inventoryBatchLinkingMode;
    private $countryOfOrigin;
    private $primaryEanCode;
    private $secondaryEanCode;
    private $inventoryAlertLimit;

    public function __construct(
        string $productGroup,
        string $name,
        float $unitPrice
    ) {
        $this->productGroup = $productGroup;
        $this->name = $name;
        $this->unitPrice = new AttributeElement(
            round($unitPrice, 2),
            ['type' => self::UNIT_PRICE_TYPE_WITHOUT_VAT]
        );
    }

    public function setProductCode(string $productCode): self
    {
        $this->productCode = $productCode;
        return $this;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function setUnit(string $unit): self
    {
        $this->unit = $unit;
        return $this;
    }

    public function setUnitWeight(float $unitWeight): self
    {
        $this->unitWeight = round($unitWeight, 2);
        return $this;
    }

    public function setPurchasePrice(float $purchasePrice): self
    {
        $this->purchasePrice = round($purchasePrice, 2);
        return $this;
    }

    public function setTariffHeading(float $tariffHeading): self
    {
        $this->tariffHeading = $tariffHeading;
        return $this;
    }

    public function setComissionPercentage(int $comissionPercentage): self
    {
        $this->comissionPercentage = $comissionPercentage;
        return $this;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive ? 1 : 0;
        return $this;
    }

    public function setIsSalesProduct(bool $isSalesProduct): self
    {
        $this->isSalesProduct = $isSalesProduct ? 1 : 0;
        return $this;
    }

    public function setInventoryEnabled(bool $inventoryEnabled): self
    {
        $this->inventoryEnabled = $inventoryEnabled ? 1 : 0;
        return $this;
    }

    public function setInventoryBatchLinkingMode(int $inventoryBatchLinkingMode): self
    {
        $allowed = [
            static::BATCH_PROCESSING_NONE,
            static::BATCH_PROCESSING_MANUAL,
            static::BATCH_PROCESSING_DELIVERY_NEWEST,
            static::BATCH_PROCESSING_DELIVERY_OLDEST,
            static::BATCH_PROCESSING_USEBY_NEWEST,
            static::BATCH_PROCESSING_USEBY_OLDEST,
            static::BATCH_PROCESSING_MANUFACTURING_NEWEST,
            static::BATCH_PROCESSING_MANUFACTURING_OLDEST,
        ];

        if (!in_array($inventoryBatchLinkingMode, $allowed)) {
            throw new \Exception('Invalid inventory batch linking mode: ' . $inventoryBatchLinkingMode);
        }

        $this->inventoryBatchLinkingMode = $inventoryBatchLinkingMode;
        return $this;
    }

    public function setCountryOfOriginCode(string $countryOfOrigin): self
    {
        $this->countryOfOrigin = $countryOfOrigin;
        return $this;
    }

    public function setPrimaryEanCode(string $primaryEanCode, ?string $type = null): self
    {
        if (!$type) {
            $type = self::EAN_CODE_TYPE_ANY;
        }

        $allowedTypes = [
            self::EAN_CODE_TYPE_ANY,
            self::EAN_CODE_TYPE_EAN8,
            self::EAN_CODE_TYPE_EAN13,
            self::EAN_CODE_TYPE_CODE128,
        ];

        if (!in_array($type, $allowedTypes)) {
            throw new \Exception('Invalid primary ean code type: ' . $type);
        }

        $this->primaryEanCode = new AttributeElement(
            $primaryEanCode,
            ['type' => $type]
        );
        return $this;
    }

    public function setSecondaryEanCode(string $secondaryEanCode, ?string $type = null): self
    {
        if (!$type) {
            $type = self::EAN_CODE_TYPE_ANY;
        }

        $allowedTypes = [
            self::EAN_CODE_TYPE_ANY,
            self::EAN_CODE_TYPE_EAN8,
            self::EAN_CODE_TYPE_EAN13,
            self::EAN_CODE_TYPE_CODE128,
        ];
        if (!in_array($type, $allowedTypes)) {
            throw new \Exception('Invalid primary ean code type: ' . $type);
        }

        $this->secondaryEanCode = new AttributeElement(
            $secondaryEanCode,
            ['type' => $type]
        );
        return $this;
    }

    public function setInventoryAlertLimit(int $inventoryAlertLimit): self
    {
        $this->inventoryAlertLimit = $inventoryAlertLimit;
        return $this;
    }
}
