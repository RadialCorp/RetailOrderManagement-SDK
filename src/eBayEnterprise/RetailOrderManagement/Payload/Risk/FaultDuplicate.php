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
class FaultDuplicate implements IFaultDuplicate
{
    use TTopLevelPayload, TOrderEvent;
    protected $_code;
    protected $_description;
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
	    '_code' => 'string(x:Code)',
	    'storeId' => 'string(x:StoreId)',
	    '_description' => 'string(x:Description)',
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
        return $this->serializeFaultDuplicate();
    }
    /**
     *
     * @return string
     */
    protected function serializeFaultDuplicate()
    {
        return sprintf(
	    '<OrderId>%s</OrderId>' .
	    '<Code>%s</Code>' .
	    '<StoreId>%s</StoreId>' . 
	    '<Description>%s</Description>',
	    $this->xmlEncode($this->getOrderId()),
	    $this->xmlEncode($this->getCode()),
	    $this->xmlEncode($this->getStoreId()),
	    $this->xmlEncode($this->getDescription())
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
	   
    public function getSessionId()
    {
	return $this->_sessionId;
    }

    public function setSessionId($sessionId)
    {
	$this->_sessionId = $sessionId;
	return $this;
    }

    public function getCode()
    {
	return $this->_code;
    }

    public function setCode($code)
    {
	$this->_code = $code;
        return $this;
    }

    public function getDescription()
    {
	return $this->_description;
    }
 
    public function setDescription($description)
    {
        $this->_description = $description;
        return $this;
    }
}
