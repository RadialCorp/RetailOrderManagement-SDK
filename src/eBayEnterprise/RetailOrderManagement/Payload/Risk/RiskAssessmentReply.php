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
	    'transactionDeviceInfo' => 'string(x:TransactionDeviceInfo)',
	    '_mockOrderEvent' => 'string(x:MockOrderEvent)',
	    '_responseCode' => 'string(x:ResponseCode)',
	    'storeId' => 'string(x:StoreId)',
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
	    '<MockOrderEvent>%s</MockOrderEvent>' .
	    '<ResponseCode>%s</ResponseCode>' .
	    $this->xmlEncode($this->getMockOrderEvent()),
	    $this->xmlEncode($this->getResponseCode())
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
	   
    protected function getRootAttributes()
    {
        return [
            '_sessionId' => $this->getSessionId(),
        ];
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

    public function getSessionId()
    {
	return $this->_sessionId;
    }

    public function setSessionId($sessionId)
    {
	$this->_sessionId = $sessionId;
	return $this;
    }
}
