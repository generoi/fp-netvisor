<?php

namespace Xi\Netvisor\Resource\Xml;

class ProductBookkeepingDetails
{
    private $defaultVatPercentage;
    private $defaultDomesticAccountNumber;
    private $defaultEuAccountNumber;
    private $defaultOutsideEuAccountNumber;

    public function __construct(
        int $defaultVatPercentage
    ) {
        $this->defaultVatPercentage = $defaultVatPercentage;
    }

    public function setDefaultDomesticAccountNumber(int $accountNumber): self
    {
        $this->defaultDomesticAccountNumber = $accountNumber;
        return $this;
    }

    public function setDefaultEuAccountNumber(int $accountNumber): self
    {
        $this->defaultEuAccountNumber = $accountNumber;
        return $this;
    }

    public function setDefaultOutsideEuAccountNumber(int $accountNumber): self
    {
        $this->defaultOutsideEuAccountNumber = $accountNumber;
        return $this;
    }
}
