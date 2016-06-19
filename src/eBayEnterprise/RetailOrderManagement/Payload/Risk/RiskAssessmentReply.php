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
namespace eBayEnterprise\RetailOrderManagement\Payload\Risk;
use eBayEnterprise\RetailOrderManagement\Payload\ISchemaValidator;
use eBayEnterprise\RetailOrderManagement\Payload\IValidatorIterator;
use eBayEnterprise\RetailOrderManagement\Payload\IPayloadMap;
use eBayEnterprise\RetailOrderManagement\Payload\IPayload;
use eBayEnterprise\RetailOrderManagement\Payload\TTopLevelPayload;

use eBayEnterprise\RetailOrderManagement\Payload\OrderEvents\TOrderEvent;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
class RiskAssessmentReply implements IRiskAssessmentReply
{
    use TTopLevelPayload, TOrderEvent;
    protected $_mockOrderEvent;
    protected $_responseCode;
    protected $_sessionId;
    protected $_reasonCode;
    protected $_reasonCodeDescription;

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
            'orderId' => 'string(x:OrderId)',
	    '_responseCode' => 'string(x:ResponseCode)',
	    'storeId' => 'string(x:StoreId)',
        ];
	$this->optionalExtractionPaths = [
	    'transactionDeviceInfo' => 'string(x:TransactionDeviceInfo)',
	    '_mockOrderEvent' => 'string(x:MockOrderEvent)',
	    '_reasonCode' => 'string(x:ReasonCode)',
	    '_reasonCodeDescription' => 'string(x:ReasonCodeDescription)',
	    '_sessionId' => 'string(@sessionId)',
	];
    }
    /**
     * Serialize the various parts of the payload into XML strings and
     * simply concatenate them together.
     * @return string
     */
    protected function serializeContents()
    {
        return $this->serializeRiskReply();
    }
    /**
     *
     * @return string
     */
    protected function serializeRiskReply()
    {
        return sprintf(
	    '<OrderId>%s</OrderId>' . $this->serializeMockOrderEvent() .
	    '<ResponseCode>%s</ResponseCode>' .
	    '<StoreId>%s</StoreId>' . $this->serializeReasonCode() . $this->serializeReasonCodeDescription(),
	    $this->xmlEncode($this->getCustomerOrderId()),
	    $this->xmlEncode($this->getResponseCode()),
	    $this->xmlEncode($this->getStoreId())
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
     * Return the name of the xml root node.
     *
     * @return string
     */
    protected function getRootNodeName()
    {
        return static::ROOT_NODE;
    }

    public function getEventType()
    {
        return static::ROOT_NODE;
    }
	   
    public function getMockOrderEvent()
    {
	return $this->_mockOrderEvent;
    }
 
    public function setMockOrderEvent($mockOrderEvent)
    {
	$this->_mockOrderEvent = $mockOrderEvent;
	return $this;
    }

    public function getResponseCode()
    {
	return $this->_responseCode;
    }

    public function setResponseCode($responseCode)
    {
	$this->_responseCode = $responseCode;
	return $this;
    }

    public function getReasonCode()
    {
        return $this->_reasonCode;
    }

    public function setReasonCode($reasonCode)
    {
        $this->_reasonCode = $reasonCode;
        return $this;
    }

    public function getReasonCodeDescription()
    {
        return $this->_reasonCodeDescription;
    }

    public function setReasonCodeDescription($reasonCodeDescription)
    {
        $this->_reasonCodeDescription = $reasonCodeDescription;
        return $this;
    }

    public function getSessionId()
    {
	return $this->_sessionId;
    }

    public function setSessionId($sessionId)
    {
	$this->_sessionId = $sessionId;
	return $this;
    }

    protected function serializeReasonCode()
    {
        $reasonCode = $this->getReasonCode();
        return $reasonCode ? "<ReasonCode>{$this->xmlEncode($reasonCode)}</ReasonCode>" : '';
    }

    protected function serializeReasonCodeDescription()
    {
        $reasonCodeDesc = $this->getReasonCodeDescription();
        return $reasonCodeDesc ? "<ReasonCodeDescription>{$this->xmlEncode($reasonCodeDesc)}</ReasonCodeDescription>" : '';
    }

    protected function serializeMockOrderEvent()
    {
	$mockOrderEvent = $this->getMockOrderEvent();
	return $mockOrderEvent ? "<MockOrderEvent>{$this->xmlEncode($mockOrderEvent)}</MockOrderEvent>" : '';
    }
}
