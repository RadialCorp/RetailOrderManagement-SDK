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

class AuthCancel implements IAuthCancel
{
    use TTopLevelPayload, TPaymentContext, TOrderEvent;

    protected $tenderType;
    protected $responseCode;
    protected $amount;
    protected $extendedResponseCode;
    protected $storeId;
    protected $sessionId;
    protected $currencyCode; 

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
	    'tenderType' => 'string(x:TenderType)',
	    'responseCode' => 'string(x:ResponseCode)',
            'amount' => 'string(x:Amount)',
	    'currencyCode' => 'string(x:Amount/@currencyCode)',
	    'orderId' => 'string(x:PaymentContext/x:OrderId|x:PaymentContextBase/x:OrderId)',
	];
        $this->optionalExtractionPaths = [
            'extendedResponseCode' => 'x:ExtendedResponseCode',
	    'storeId' => 'x:StoreId',
	    'sessionId' => '@sessionId',
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
            . $this->serializeAuthCancel();
    }

    protected function serializeAuthCancel()
    {
        return sprintf(
            '<TenderType>%s</TenderType>'.
	    '<ResponseCode>%s</ResponseCode>' .
            '<Amount currencyCode="%s">%.2f</Amount>'.
            $this->xmlEncode($this->getTenderType()),
	    $this->xmlEncode($this->getResponseCode()),
            $this->xmlEncode($this->getCurrencyCode()),
            $this->getAmount()
        ) . $this->serializeOptionalXmlEncodedValue('ExtendedResponseCode', $this->getExtendedResponseCode())
          . $this->serializeOptionalXmlEncodedValue('StoreId', $this->getStoreId());
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

    public function getResponseCode()
    {
	return $this->responseCode;
    }

    public function setResponseCode($value)
    {
	$this->responseCode = $value;
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

    public function getExtendedResponseCode()
    {
	return $this->extendedResponseCode;
    }

    public function setExtendedResponseCode($value)
    {
	$this->extendedResponseCode = $value;
	return $this;
    }

    public function getStoreId()
    {
	return $this->storeId;
    }

    public function setStoreId($value)
    {
	$this->storeId = $value;
	return $this;
    }

    public function getSessionId()
    {
        return $this->sessionId;
    }

    public function setSessionId($sessionId)
    {
        $this->sessionId = $this->cleanString($sessionId, 40);
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
