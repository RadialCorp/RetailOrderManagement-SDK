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

namespace eBayEnterprise\RetailOrderManagement\Payload\OrderEvents;

use eBayEnterprise\RetailOrderManagement\Payload\IPayload;
use eBayEnterprise\RetailOrderManagement\Payload\IPayloadMap;
use eBayEnterprise\RetailOrderManagement\Payload\ISchemaValidator;
use eBayEnterprise\RetailOrderManagement\Payload\IValidatorIterator;
use eBayEnterprise\RetailOrderManagement\Payload\Payment\TPaymentContext;
use eBayEnterprise\RetailOrderManagement\Payload\TTopLevelPayload;
use Psr\Log\LoggerInterface;

class SettlementStatus implements ISettlementStatus
{
    use TTopLevelPayload, TPaymentContext, TOrderEvent;

    protected $currencyCode;
    protected $declineReason;
    protected $settlementStatus;
    protected $settlementType;
    protected $amount;
    protected $tenderType;
    protected $requestId;

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
        $this->parentPayload = $parentPayload;
        $this->eventType = self::EVENT_TYPE;
        $this->extractionPaths = [
            'amount' => 'string(x:Amount)',
            'currencyCode' => 'string(x:Amount/@currencyCode)',
            'orderId' => 'string(x:PaymentContext/x:OrderId|x:PaymentContextBase/x:OrderId)',
            'settlementType' => 'string(x:SettlementType)',
            'settlementStatus' => 'string(x:SettlementStatus)',
        ];
        $this->optionalExtractionPaths = [
            'declineReason' => 'x:DeclineReason',
            'tenderType' => 'string(x:TenderType)',
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

    protected function serializeSettlementInfo()
    {
        return sprintf(
            '<TenderType>%s</TenderType>'.
            '<Amount currencyCode="%s">%.2f</Amount>'.
            '<SettlementType>%s</SettlementType>'.
            '<SettlementStatus>%s</SettlementStatus>',
            $this->xmlEncode($this->getTenderType()),
            $this->xmlEncode($this->getCurrencyCode()),
            $this->getAmount(),
            $this->xmlEncode($this->getSettlementType()),
            $this->xmlEncode($this->getSettlementStatus())
        );
    }


    public function getTenderType()
    {
        return $this->tenderType;
    }

    public function setTenderType($value)
    {
        $this->tenderType = $value;
        return $this;
    }

    public function getAmount()
    {
        return $this->amount;
    }

    public function setAmount($value)
    {
        $this->amount = $value;
        return $this;
    }

    public function getSettlementType()
    {
        return $this->settlementType;
    }

    public function setSettlementType($value)
    {
        $this->settlementType = $value;
    }

    public function getSettlementStatus()
    {
        return $this->settlementStatus;
    }

    public function setSettlementStatus($value)
    {
        $this->settlementStatus = $value;
        return $this;
    }

    public function getDeclineReason()
    {
        return $this->declineReason;
    }

    public function setDeclineReason($value)
    {
        $this->declineReason = $value;
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

    public function getRequestId()
    {
        return $this->requestId;
    }

    public function setRequestId($requestId)
    {
        $this->requestId = $this->cleanString($requestId, 40);
        return $this;
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
}
