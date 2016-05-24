<?php
/**
 * Copyright (c) 2013-2014 eBay Enterprise, Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @copyright   Copyright (c) 2013-2014 eBay Enterprise, Inc. (http://www.ebayenterprise.com/)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace eBayEnterprise\RetailOrderManagement\Payload\Payment;

use eBayEnterprise\RetailOrderManagement\Payload\ISchemaValidator;
use eBayEnterprise\RetailOrderManagement\Payload\IValidatorIterator;
use eBayEnterprise\RetailOrderManagement\Payload\IPayloadMap;
use eBayEnterprise\RetailOrderManagement\Payload\IPayload;
use eBayEnterprise\RetailOrderManagement\Payload\TTopLevelPayload;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class PaymentSettlementRequest implements IPaymentSettlementRequest
{
    use TTopLevelPayload, TPaymentContext;

    protected $currencyCode;
    protected $requestId;
    protected $amount;
    protected $taxAmount;
    protected $settlementType;
    protected $clientContext;
    protected $finalDebit;
    protected $invoiceId;

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
        $this->parentPayload = $parentPayload;

        $this->extractionPaths = [
            'requestId' => 'string(@requestId)',
            'cardNumber' =>
                'string(x:PaymentContext/x:EncryptedPaymentAccountUniqueId|x:PaymentContext/x:PaymentAccountUniqueId)',
            'orderId' => 'string(x:PaymentContext/x:OrderId|x:PaymentContextBase/x:OrderId)',
            'currencyCode' => 'string(x:Amount/@currencyCode)',
            'amount' => 'number(x:Amount)',
            'taxAmount' => 'number(x:TaxAmount)',
            'clientContext' => 'number(x:ClientContext)',
            'settlementType' => 'string(x:SettlementType)',
            'invoiceId' => 'string(x:InvoiceId)',
        ];
        $this->booleanExtractionPaths = [
            'finalDebit' => 'boolean(x:FinalDebit)'
        ];
    }

    /**
     * Serialize the various parts of the payload into XML strings and
     * simply concatenate them together.
     * @return string
     */
    protected function serializeContents()
    {
        return $this->getCardNumber() ?
            $this->serializePaymentContext() :
            $this->serializePaymentContextBase() 
        . $this->serializeSettlementInfo();
    }

    /**
     * Build the ClientContext, SettlementType, and Amount nodes
     *
     * @return string
     */
    protected function serializeSettlementInfo()
    {
        return sprintf(
            '<InvoiceId>%s</InvoiceId>'.
            '<Amount currencyCode="%s">%.2f</Amount>'.
            '<TaxAmount currencyCode="%s">%.2f</TaxAmount>'.
            '<SettlementType>%s</SettlementType>'.
            '<ClientContext>%s</ClientContext>'.
            '<FinalDebit>%s</FinalDebit>',
            $this->xmlEncode($this->getInvoiceId()),
            $this->xmlEncode($this->getCurrencyCode()),
            $this->getAmount(),
            $this->xmlEncode($this->getCurrencyCode()),
            $this->getTaxAmount(),
            $this->xmlEncode($this->getSettlementType()),
            $this->xmlEncode($this->getClientContext()),
            $this->xmlEncode($this->getFinalDebit())
        );
    }

    /**
     * The XML namespace for the payload.
     *
     * @return string
     */
    protected function getXmlNamespace()
    {
        return static::XML_NS;
    }

    protected function getSchemaFile()
    {
        return $this->getSchemaDir() . self::XSD;
    }

    /**
     * Name, value pairs of root attributes
     *
     * @return array
     */
    protected function getRootAttributes()
    {
        return [
            'xmlns' => $this->getXmlNamespace(),
            'requestId' => $this->getRequestId(),
        ];
    }

    /**
     * Return the name of the xml root node.
     *
     * @return string
     */
    protected function getRootNodeName()
    {
        return static::ROOT_NODE;
    }

    public function getRequestId()
    {
        return $this->requestId;
    }

    public function setRequestId($requestId)
    {
        $this->requestId = $this->cleanString($requestId, 40);
        return $this;
    }

    public function getAmount()
    {
        return $this->amount;
    }

    public function setAmount($amount)
    {
        if (is_float($amount)) {
            $this->amount = round($amount, 2, PHP_ROUND_HALF_UP);
        } else {
            $this->amount = null;
        }
        return $this;
    }

    public function getCurrencyCode()
    {
        return $this->currencyCode;
    }

    public function setCurrencyCode($code)
    {
        $value = null;

        $cleaned = $this->cleanString($code, 3);
        if ($cleaned !== null) {
            if (!strlen($cleaned) < 3) {
                $value = $cleaned;
            }
        }
        $this->currencyCode = $value;

        return $this;
    }

    public function getTaxAmount()
    {
        return $this->taxAmount;
    }

    public function setTaxAmount($amount)
    {
        if (is_float($amount)) {
            $this->taxAmount = round($amount, 2, PHP_ROUND_HALF_UP);
        } else {
            $this->taxAmount = null;
        }
        return $this;
    }

    public function getSettlementType()
    {
        return $this->settlementType;
    }

    public function setSettlementType($type)
    {
        $this->settlementType = $type;
        return $this;
    }

    public function getClientContext()
    {
        return $this->clientContext;
    }

    public function setClientContext($context)
    {
        $this->clientContext = $context;
        return $this;
    }

    public function getFinalDebit()
    {
        return $this->finalDebit;
    }

    public function setFinalDebit($finalDebit)
    {
        $this->finalDebit = $finalDebit;
        return $this;
    }

    public function getInvoiceId()
    {
        return $this->invoiceId;
    }

    public function setInvoiceId($invoiceId)
    {
        $this->invoiceId = $invoiceId;
        return $this;
    }
}
