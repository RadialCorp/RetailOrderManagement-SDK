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

class ConfirmFundsRequest implements IConfirmFundsRequest
{
    use TTopLevelPayload, TPaymentContext;

    protected $currencyCode;
    protected $requestId;
    protected $amount;
    protected $performReauthorization;

    /**
     * Validate the serialized data via the schema validator.
     * @param  string $serializedData
     * @return $this
     */
    protected function schemaValidate($serializedData)
    {
        return $this;
    }
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
            'orderId' => 'string(x:PaymentContext/x:OrderId)',
            'currencyCode' => 'string(x:Amount/@currencyCode)',
            'amount' => 'number(x:Amount)',
        ];
	$this->booleanExtractionPaths = [
            'performReauthorization' => 'boolean(x:PerformReauthorization)'
        ];
    }

    /**
     * Serialize the various parts of the payload into XML strings and
     * simply concatenate them together.
     * @return string
     */
    protected function serializeContents()
    {
        $paymentContext = $this->getCardNumber() ?
            $this->serializePaymentContext() :
            $this->serializePaymentContextBase();
        return $paymentContext . $this->serializeAmount() . $this->serializePerformReauthorization();
    }

    /**
     * Build the Amount node
     *
     * @return string
     */
    protected function serializeAmount()
    {
        return sprintf(
            '<Amount currencyCode="%s">%.2f</Amount>',
            $this->xmlEncode($this->getCurrencyCode()),
            $this->getAmount()
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

    public function getPerformReauthorization()
    {
	return $this->performReauthorization;
    }

    public function setPerformReauthorization($performReauthorization)
    {
	$this->performReauthorization = (bool)$performReauthorization;
	return $this;
    }

    protected function serializePerformReauthorization()
    {
	return sprintf(
            '<PerformReauthorization>%s</PerformReauthorization>',
            $this->xmlEncode($this->getPerformReauthorization())
        );
    }
}
