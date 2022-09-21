<?php

namespace Xi\Netvisor\Resource\Xml;

use Xi\Netvisor\Resource\Xml\Product;
use Xi\Netvisor\Resource\Xml\ProductBaseInformation;
use Xi\Netvisor\Resource\Xml\ProductBookkeepingDetails;
use Xi\Netvisor\XmlTestCase;

class ProductTest extends XmlTestCase
{
    /**
     * @var Product
     */
    private $product;

    /**
     * @var ProductBaseInformation
     */
    private $baseInformation;

    public function setUp(): void
    {
        parent::setUp();

        $this->baseInformation = new ProductBaseInformation(
            'Books',
            'Code Complete',
            42.5,
        );

        $this->product = new Product($this->baseInformation, null);
    }

    /**
     * @test
     */
    public function hasDtd()
    {
        $this->assertNotNull($this->product->getDtdPath());
    }

    /**
     * @test
     */
    public function xmlHasRequiredValues()
    {
        $xml = $this->toXml($this->product->getSerializableObject());

        $this->assertXmlContainsTagWithValue('name', 'Code Complete', $xml);
        $this->assertXmlIsValid($xml, $this->product->getDtdPath());
    }

    public function testSetDescription()
    {
        $description = 'Test description';
        $this->baseInformation->setDescription($description);
        $xml = $this->toXml($this->product->getSerializableObject());
        $this->assertXmlContainsTagWithValue('description', $descritpion, $xml);
    }

    public function testSetInventoryBatchLinkingMode()
    {
        $this->baseInformation->setInventoryBatchLinkingMode(2);
        $xml = $this->toXml($this->product->getSerializableObject());
        $this->assertXmlContainsTagWithValue('inventorybatchlinkingmode', 2, $xml);
    }
}
