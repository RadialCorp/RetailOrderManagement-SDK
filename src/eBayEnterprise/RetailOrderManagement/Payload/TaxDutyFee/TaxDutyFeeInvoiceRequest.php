<?php
/**
 * Copyright (c) 2014-2015 eBay Enterprise, Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @copyright   Copyright (c) 2014-2015 eBay Enterprise, Inc. (http://www.ebayenterprise.com/)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace eBayEnterprise\RetailOrderManagement\Payload\TaxDutyFee;

use DateTime;
use DOMXPath;
use eBayEnterprise\RetailOrderManagement\Payload\IPayload;
use eBayEnterprise\RetailOrderManagement\Payload\IPayloadMap;
use eBayEnterprise\RetailOrderManagement\Payload\ISchemaValidator;
use eBayEnterprise\RetailOrderManagement\Payload\IValidatorIterator;
use eBayEnterprise\RetailOrderManagement\Payload\PayloadFactory;
use eBayEnterprise\RetailOrderManagement\Payload\TTopLevelPayload;

use eBayEnterprise\RetailOrderManagement\Payload\Checkout\TDestinationContainer;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class TaxDutyFeeInvoiceRequest implements ITaxDutyFeeInvoiceRequest
{

    use TTopLevelPayload, TTaxedShipGroupContainer, TDestinationContainer, TTaxedFeeContainer;

    /** @var string */
    protected $orderId;
    /** @var string */
    protected $originalOrderId;
    /** @var string */
    protected $webOrderNumber;
    /** @var string */
    protected $invoiceNumber;
    /** @var string */
    protected $invoiceType;
    /** @var DateTime */
    protected $orderDateTime;
    /** @var DateTime */
    protected $originalOrderDateTime;
    /** @var DateTime */
    protected $shipDateTime;  
    /** @var string */
    protected $currency;
    /** @var string */
    protected $currencyConversionRate;
    /** @var bool */
    protected $vatInclusivePricingFlag;
    /** @var string */
    protected $billingInformationIdRef;
    /** @var string */
    protected $vatInvoiceNumber;
    /** @var string */
    protected $customerTaxId;
    /** @var string */
    protected $taxTransactionId;

    /**
     * @param IValidatorIterator
     * @param ISchemaValidator
     * @param IPayloadMap
     * @param LoggerInterface
     * @param IPayload
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __construct(
        IValidatorIterator $validators,
        ISchemaValidator $schemaValidator,
        IPayloadMap $payloadMap,
        LoggerInterface $logger,
        IPayload $parentPayload = null
    ) {
        $this->logger = $logger;
        $this->validators = $validators;
        $this->schemaValidator = $schemaValidator;
        $this->payloadMap = $payloadMap;
        $this->parentPayload = $parentPayload;
        $this->payloadFactory = new PayloadFactory;

        $this->shipGroups = $this->buildPayloadForInterface(
            static::TAXED_SHIP_GROUP_ITERABLE_INTERFACE
        );
        $this->destinations = $this->buildPayloadForInterface(
            static::DESTINATION_ITERABLE_INTERFACE
        );
	$this->fees = $this->buildPayloadForInterface(
            static::FEE_ITERABLE_INTERFACE
        );
        $this->extractionPaths = [
            'billingInformationIdRef' => 'string(x:BillingInformation/@ref)',
	    'orderId' => 'string(x:OrderId)',
	    'invoiceNumber' => 'string(x:InvoiceNumber)',
	    'orderDateTime' => 'string(x:OrderDateTime)',
	    'shipDateTime' => 'string(x:ShipDateTime)',
            'currency' => 'string(x:Currency)',
        ];
        $this->optionalExtractionPaths = [
            'originalOrderId' => 'x:OriginalOrderId',
	    'webOrderNumber' => 'x:WebOrderNumber',
	    'invoiceType' => 'x:InvoiceType',
	    'originalOrderDateTime' => 'x:OriginalOrderDateTime',
	    'currencyConversionRate' => 'x:CurrencyConversionRate',
	    'vatInvoiceNumber' => 'x:VATInvoiceNumber',
	    'customerTaxId' => 'x:CustomerTaxId',
	    'taxTransactionId' => 'x:TaxTransactionId',
        ];
        $this->booleanExtractionPaths = [
            'vatInclusivePricingFlag' => 'string(x:VATInclusivePricing)',
        ];
        $this->subpayloadExtractionPaths = [
            'shipGroups' => 'x:Shipping/x:ShipGroups',
            'destinations' => 'x:Shipping/x:Destinations',
	    'fees' => 'x:Fees',
        ];
    }

    /**
     * Order ID For Tax Invoice
     *
     * @return string
     */
    public function getOrderId()
    {
	return $this->orderId;
    }

    /**
     * @param string
     * @return self
     */
    public function setOrderId($orderId)
    {
   	$this->orderId = $orderId;
	return $this;
    }

    /**
     * Original Order ID For Tax Invoice
     *
     * @return string
     */
    public function getOriginalOrderId()
    {
        return $this->originalOrderId;
    }

    /**
     * @param string
     * @return self
     */
    public function setOriginalOrderId($originalOrderId)
    {
        $this->originalOrderId = $originalOrderId;
        return $this;
    }

    /**
     * Web Order Number For Tax Invoice
     *
     * @return string
     */
    public function getWebOrderNumber()
    {
        return $this->webOrderNumber;
    }

    /**
     * @param string
     * @return self
     */
    public function setWebOrderNumber($webOrderNumber)
    {
        $this->webOrderNumber = $webOrderNumber;
        return $this;
    }

    /**
     * Invoice Number for Tax Invoice
     *
     * @return string
     */
    public function getInvoiceNumber()
    {
	return $this->invoiceNumber;
    }

    /**
     * @param string
     * @return self
     */
    public function setInvoiceNumber($invoiceNumber)
    {
	$this->invoiceNumber = $invoiceNumber;
	return $this;
    }

    /**
     * Invoice Type for Tax Invoice
     *
     * @return string
     */
    public function getInvoiceType()
    {
        return $this->invoiceType;
    }

    /**
     * @param string
     * @return self
     */
    public function setInvoiceType($invoiceType)
    {
        $this->invoiceType = $invoiceType;
        return $this;
    }

    /**
     * Order Date Time
     *
     * @return \DateTime
     */
    public function getOrderDateTime()
    {
	return $this->orderDateTime;
    }

    /**
     * @param \DateTime
     * @return self
     */
    public function setOrderDateTime(DateTime $orderDateTime)
    {
	$this->orderDateTime = $orderDateTime;
	return $this;
    }

    /**
     * Order Date Time
     *
     * @return \DateTime
     */
    public function getOriginalOrderDateTime()
    {
        return $this->originalOrderDateTime;
    }

    /**
     * @param \DateTime
     * @return self
     */
    public function setOriginalOrderDateTime(DateTime $originalOrderDateTime)
    {
        $this->originalOrderDateTime = $originalOrderDateTime;
        return $this;
    }

    /**
     * Ship Date Time
     *
     * @return \DateTime
     */
    public function getShipDateTime()
    {
	return $this->shipDateTime;
    }

    /**
     * @param \DateTime
     * @return self
     */
    public function setShipDateTime(DateTime $shipDateTime)
    {
	$this->shipDateTime = $shipDateTime;
	return $this;
    }

    /**
     * Currency code for the request.
     *
     * restrictions: 2 >= length <= 40
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @param string
     * @return self
     */
    public function setCurrency($currency)
    {
        $clean = $this->cleanString($currency, 40);
        $this->currency = strlen($clean) > 1 ? $clean : null;
        return $this;
    }

    public function getCurrencyConversionRate()
    {
	return $this->currencyConversionRate;
    }

    public function setCurrencyConversionRate($currencyConversionRate)
    {
	$this->currencyConversionRate = $currencyConversionRate;
	return $this;
    }

    public function getVATInvoiceNumber()
    {
	return $this->vatInvoiceNumber;
    }

    public function setVATInvoiceNumber($vatInvoiceNumber)
    {
	$this->vatInvoiceNumber = $vatInvoiceNumber;
	return $this;
    }

    /**
     * Flag indicating prices already have VAT tax included.
     *
     * restrictions: optional
     * @return bool
     */
    public function getVatInclusivePricingFlag()
    {
        return $this->vatInclusivePricingFlag;
    }

    /**
     * @param bool
     * @return self
     */
    public function setVatInclusivePricingFlag($flag)
    {
        $this->vatInclusivePricingFlag = $flag;
        return $this;
    }

    /**
     * Tax Identifier for the customer.
     *
     * restrictions: optional
     * @return string
     */
    public function getCustomerTaxId()
    {
        return $this->customerTaxId;
    }

    /**
     * @param string
     * @return self
     */
    public function setCustomerTaxId($id)
    {
        $this->customerTaxId = $this->cleanString($id, 40);
        return $this;
    }

    /**
     * Tax Identifier for the Tax Quote.
     *
     * restrictions: optional
     * @return string
     */
    public function getTaxTransactionId()
    {
        return $this->taxTransactionId;
    }

    /**
     * @param string
     * @return self
     */
    public function setTaxTransactionId($id)
    {
        $this->taxTransactionId = $this->cleanString($id, 40);
        return $this;
    }

    /**
     * Customer billing address
     *
     * @return IDestination
     */
    public function getBillingInformation()
    {
        foreach ($this->getDestinations() as $destination) {
            if ($destination->getId() === $this->billingInformationIdRef) {
                return $destination;
            }
        }
        return isset($destination) ? $destination : null;
    }

    /**
     * @param IDestination
     * @return self
     */
    public function setBillingInformation(IDestination $billingDest)
    {
        $this->billingInformationIdRef = $billingDest->getId();
        $this->destinations->attach($billingDest);
        return $this;
    }

    /**
     * get the schema file name
     * @return string
     */
    protected function getSchemaFile()
    {
        return $this->getSchemaDir() . self::XSD;
    }

    /**
     * get the root element name
     * @return string
     */
    protected function getRootNodeName()
    {
    	return static::ROOT_NODE;
    }

    protected function serializeBillingInformation()
    {
        $billingInformation = $this->getBillingInformation();
        if ($billingInformation) {
            return "<BillingInformation ref='{$this->xmlEncode($this->billingInformationIdRef)}'></BillingInformation>";
        }
        return '';
    }

    protected function serializeCurrency()
    {
        return "<Currency>{$this->xmlEncode($this->getCurrency())}</Currency>";
    }

    protected function getXmlNameSpace()
    {
        return static::XML_NS;
    }

    protected function serializeShipping()
    {
        return '<Shipping>'
            . $this->getShipGroups()->serialize()
            . $this->getDestinations()->serialize()
            . '</Shipping>';

    }

    protected function serializeVatInclusivePricing()
    {
        $flag =$this->getVatInclusivePricingFlag();
        if ($flag === true || $flag === false) {
            $flag = $this->convertBooleanToString($flag);
            return "<VATInclusivePricing>$flag</VATInclusivePricing>";
        }
        return '';
    }

    protected function serializeContents()
    {
        $contents =
	    $this->serializeXmlEncodedValue('OrderId', $this->getOrderId())
	    . $this->serializeOptionalXmlEncodedValue('OriginalOrderId', $this->getOriginalOrderId())
	    . $this->serializeOptionalXmlEncodedValue('WebOrderNumber', $this->getWebOrderNumber())
	    . $this->serializeXmlEncodedValue('InvoiceNumber', $this->getInvoiceNumber())
	    . $this->serializeOptionalXmlEncodedValue('InvoiceType', $this->getInvoiceType())
	    . $this->serializeOptionalDateValue('OrderDateTime', 'c', $this->getOrderDateTime())
	    . $this->serializeOptionalDateValue('OriginalOrderDateTime', 'c', $this->getOriginalOrderDateTime())
	    . $this->serializeOptionalDateValue('ShipDateTime', 'c', $this->getShipDateTime())
	    . $this->serializeCurrency()
	    . $this->serializeOptionalXmlEncodedValue('CurrencyConversionRate', $this->getCurrencyConversionRate())
	    . $this->serializeOptionalXmlEncodedValue('VATInvoiceNumber', $this->getVATInvoiceNumber())
            . $this->serializeVatInclusivePricing()
            . $this->serializeOptionalXmlEncodedValue('CustomerTaxId', $this->getCustomerTaxId())
	    . $this->serializeOptionalXmlEncodedValue('TaxTransactionId', $this->getTaxTransactionId())
            . $this->serializeBillingInformation()
            . $this->serializeShipping()
 	    . $this->getFees()->serialize();
        return $contents;
    }
}
