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

use eBayEnterprise\RetailOrderManagement\Payload\IPayload;
use eBayEnterprise\RetailOrderManagement\Payload\IPayloadMap;
use eBayEnterprise\RetailOrderManagement\Payload\ISchemaValidator;
use eBayEnterprise\RetailOrderManagement\Payload\IValidatorIterator;
use eBayEnterprise\RetailOrderManagement\Payload\TTopLevelPayload;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class ConfirmFundsReply implements IConfirmFundsReply
{
    const SUCCESS = 'Success';
    const TIMEOUT = 'Timeout';

    use TTopLevelPayload, TPaymentContext;

    /** @var string * */
    protected $fundsAvailable;
    /** @var boolean **/
    protected $reauthorizationAttempted;

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
            'orderId' => 'string(x:PaymentContext/x:OrderId|x:PaymentContextBase/x:OrderId)',
            'cardNumber' =>
                'string(x:PaymentContext/x:EncryptedPaymentAccountUniqueId|x:PaymentContext/x:PaymentAccountUniqueId)',
            'fundsAvailable' => 'string(x:FundsAvailable)',
        ];
	$this->booleanExtractionPaths = [
            'reauthorizationAttempted' => 'boolean(x:ReauthorizationAttempted)'
        ];
    }

    /**
     * Should downstream systems consider this reply a success?
     *
     * @return bool
     */
    public function isSuccess()
    {
        return ($this->getFundsAvailable() === static::SUCCESS);
    }

    /**
     * Did upstream systems report a timeout?
     *
     * @return bool
     */
    public function isTimeout()
    {
        return ($this->getFundsAvailable() === static::TIMEOUT);
    }

    /**
     * Response code like Success, Failure etc
     *
     * @return string
     */
    public function getFundsAvailable()
    {
        return $this->fundsAvailable;
    }

    /**
     * @param string
     * @return self
     */
    public function setResponseCode($fundsAvailable)
    {
        $this->fundsAvailable = $fundsAvailable;
        return $this;
    }

    /**
     * Return the string form of the payload data for transmission.
     * Validation is implied.
     *
     * @return string
     */
    protected function serializeContents()
    {
        $paymentContext = $this->getCardNumber() ?
            $this->serializePaymentContext() :
            $this->serializePaymentContextBase();
        return $paymentContext .
        "<FundsAvailable>{$this->xmlEncode($this->getFundsAvailable())}</FundsAvailable>" . $this->serializeReauthorizationAttempted();
    }

    /**
     * Return the schema file path.
     * @return string
     */
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

    /**
     * The XML namespace for the payload.
     *
     * @return string
     */
    protected function getXmlNamespace()
    {
        return static::XML_NS;
    }

    public function getReauthorizationAttempted()
    {
	return $this->reauthorizationAttempted;
    }

    public function setReauthorizationAttempted($reauthorizationAttempted)
    {
	$this->reauthorizationAttempted = (bool)$reauthorizationAttempted;
	return $this;
    }
 
    protected function serializeReauthorizationAttempted()
    {
        return sprintf(
            '<ReauthorizationAttempted>%s</ReauthorizationAttempted>',
            $this->xmlEncode($this->getReauthorizationAttempted())
        );
    }
}
