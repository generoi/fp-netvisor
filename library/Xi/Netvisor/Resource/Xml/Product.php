<?php

namespace Xi\Netvisor\Resource\Xml;

use Xi\Netvisor\Resource\Xml\Component\Root;

class Product extends Root
{
    private $productBaseInformation;
    private $productBookkeepingDetails;
    // private $productAdditionalInformation;
    // private $productPackageInformation;
    // private $productCustomTags; @todo

    public function __construct(
        ProductBaseInformation $productBaseInformation,
        ProductBookkeepingDetails $productBookkeepingDetails = null
        // ProductAdditionalInformation $productAdditionalInformation = null,
        // ProductPackageInformation $productPackageInformation = null
    ) {
        parent::__construct();

        $this->productBaseInformation = $productBaseInformation;
        $this->productBookkeepingDetails = $productBookkeepingDetails;
        // $this->productAdditionalInformation = $productAdditionalInformation;
        // $this->productPackageInformation = $productPackageInformation;
    }

    public function getDtdPath()
    {
        return $this->getDtdFile('product.dtd');
    }

    protected function getXmlName()
    {
        return 'product';
    }
}
