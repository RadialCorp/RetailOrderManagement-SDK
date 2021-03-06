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

namespace eBayEnterprise\RetailOrderManagement\Payload\Inventory;

use eBayEnterprise\RetailOrderManagement\Payload\IPayload;
use eBayEnterprise\RetailOrderManagement\Payload\IPayloadMap;
use eBayEnterprise\RetailOrderManagement\Payload\ISchemaValidator;
use eBayEnterprise\RetailOrderManagement\Payload\IValidatorIterator;
use eBayEnterprise\RetailOrderManagement\Payload\PayloadFactory;
use eBayEnterprise\RetailOrderManagement\Payload\TTopLevelPayload;
use Psr\Log\LoggerInterface;

/**
 * Reserves inventory for an order
 */
class AllocationRequest implements IAllocationRequest
{
    use TTopLevelPayload;

    const ROOT_NODE = 'AllocationRequestMessage';

    /** @var string */
    protected $reservationId;
    /** @var string */
    protected $requestId;
    /** @var IItemIterable */
    protected $items;

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
        list(
            $this->validators,
            $this->schemaValidator,
            $this->payloadMap,
            $this->logger,
            $this->parentPayload
        ) = func_get_args();
        $this->payloadFactory = $this->getNewPayloadFactory();

        $this->items = $this->buildPayloadForInterface(
            static::ITEM_ITERABLE_INTERFACE
        );
        $this->extractionPaths = [
            'reservationId' => 'string(@reservationId)',
            'requestId' => 'string(@requestId)',
        ];
    }

    /**
     * Uniquely identifies a request operation
     *
     * restrictions: required, 1 < length <= 40
     * @return string
     */
    public function getRequestId()
    {
        return $this->requestId;
    }

    /**
     * @param string
     * @return self
     */
    public function setRequestId($requestId)
    {
        $this->requestId = $requestId;
        return $this;
    }

    /**
     * Identifies the inventory reservation which is created by this operation.
     *
     * restrictions: optional
     * @return string
     */
    public function getReservationId()
    {
        return $this->reservationId;
    }

    /**
     * @param string
     * @return self
     */
    public function setReservationId($reservationId)
    {
        $this->reservationId = $this->cleanString($reservationId, 40);
        return $this;
    }

    /**
     * Collection of items and their allocation status.
     *
     * @return IItemIterable
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @param IItemIterable
     * @return self
     */
    public function setItems(IItemIterable $items)
    {
        $this->items = $items;
        return $this;
    }

    /**
     * @see eBayEnterprise\RetailOrderManagement\Payload\TTopLevelPayload::getSchemaFile
     * @return string
     */
    protected function getSchemaFile()
    {
        return $this->getSchemaDir() . static::XSD;
    }

    /**
     * @see eBayEnterprise\RetailOrderManagement\Payload\TTopLevelPayload::getRootAttributes
     * @return array
     */
    protected function getRootAttributes()
    {
        return [
            'xmlns' => $this->getXmlNamespace(),
            'requestId' => $this->getRequestId(),
            'reservationId' => $this->getReservationId(),
        ];
    }

    /**
     * @see eBayEnterprise\RetailOrderManagement\Payload\TPayload::getRootNodeName
     * @return string
     */
    protected function getRootNodeName()
    {
        return static::ROOT_NODE;
    }

    /**
     * @see eBayEnterprise\RetailOrderManagement\Payload\TPayload::serializeContents
     * @return string
     */
    protected function serializeContents()
    {
        return $this->getItems()->serialize();
    }

    /**
     * @see eBayEnterprise\RetailOrderManagement\Payload\TPayload::deserializeLineItems
     * @param string
     * @return self
     */
    protected function deserializeLineItems($serializedPayload)
    {
        $items = $this->getItems();
        $xpath = $this->getPayloadAsXPath($serializedPayload);
        foreach ($xpath->query('x:OrderItem/x:ShipmentDetails/..') as $itemNode) {
            $item = $items->getEmptyShippingItem();
            $item->deserialize($itemNode->C14N());
            $items->attach($item);
        }
        foreach ($xpath->query('x:OrderItem/x:InStorePickupDetails/..') as $itemNode) {
            $item = $items->getEmptyInStorePickUpItem();
            $item->deserialize($itemNode->C14N());
            $items->attach($item);
        }
        return $this;
    }

    /**
     * @see eBayEnterprise\RetailOrderManagement\Payload\TPayload::getXmlNamespace
     * @return string
     */
    protected function getXmlNamespace()
    {
        return static::XML_NS;
    }
}
