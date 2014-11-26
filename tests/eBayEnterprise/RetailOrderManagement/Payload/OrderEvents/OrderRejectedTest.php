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

use eBayEnterprise\RetailOrderManagement\Payload;
use eBayEnterprise\RetailOrderManagement\Util\TTestReflection;
use DateTime;

class OrderRejectedTest extends \PHPUnit_Framework_TestCase
{
    use TTestReflection;
    /** @var Payload\IValidator (stub) */
    protected $stubValidator;
    /** @var Payload\IValidatorIterator */
    protected $validatorIterator;
    /** @var Payload\ISchemaValidator (stub) */
    protected $stubSchemaValidator;
    /**
     * Setup a stub validator and validator iterator for each payload to use
     */
    public function setUp()
    {
        // use stub to allow validation success/failure to be scripted.
        $this->stubValidator = $this->getMock('\eBayEnterprise\RetailOrderManagement\Payload\IValidator');
        $this->validatorIterator = new Payload\ValidatorIterator([$this->stubValidator]);
        $this->stubSchemaValidator = $this->getMock('\eBayEnterprise\RetailOrderManagement\Payload\ISchemaValidator');
    }
    /**
     * Get a new OrderRejected payload. Each payload will contain a
     * ValidatorIterator (self::validatorIterator) containing a single mocked
     * validator (self::$stubValidator).
     * @return OrderRejected
     */
    protected function createNewPayload()
    {
        return new OrderRejected($this->validatorIterator, $this->stubSchemaValidator);
    }
    /**
     * Data provider to build `OrderRejected` payload data
     * @return array[]
     */
    public function providerSerialize()
    {
        return [
            [
               '10000001',
               'GTA24',
               '2014-07-06T06:09:05-04:00',
               'Testing invalid payment reason message',
               'Invalid Payment',
               '<OrderRejected xmlns="http://api.gsicommerce.com/schema/checkout/1.0" customerOrderId="10000001" storeId="GTA24" orderCreateTimestamp="2014-07-06T06:09:05-04:00"><Reason code="Invalid Payment">Testing invalid payment reason message</Reason></OrderRejected>'

            ],
        ];
    }
    /**
     * Data provider to build `OrderRejected` payload using serialize xml
     * @return array[]
     */
    public function providerDeserialize()
    {
        return [
            [
               '<OrderRejected xmlns="http://api.gsicommerce.com/schema/checkout/1.0" customerOrderId="10000001" storeId="GTA24" orderCreateTimestamp="2014-07-06T06:09:05-04:00"><Reason code="Invalid Shipment">Testing invalid shipment reason message</Reason></OrderRejected>',
               '10000001',
               'GTA24',
               '2014-07-06T06:09:05-04:00',
               'Testing invalid shipment reason message',
               'Invalid Shipment',
            ],
        ];
    }
    /**
     * Test the `OrderRejected` serialized as expected.
     * @param string $customerOrderId
     * @param string $storeId
     * @param string $orderCreateTimestamp
     * @param string $reason
     * @param string $code
     * @param string $result the expected serialized xml string
     * @dataProvider providerSerialize
     */
    public function testOrderRejectedSerialize($customerOrderId, $storeId, $orderCreateTimestamp, $reason, $code, $result)
    {
        $payload = $this->createNewPayload();
        $payload->setCustomerOrderId($customerOrderId)
            ->setStoreId($storeId)
            ->setOrderCreateTimestamp(new DateTime($orderCreateTimestamp))
            ->setReason($reason)
            ->setCode($code);
        $this->assertSame($result, $payload->serialize());
    }
    /**
     * Test the `OrderRejected` unserialized as expected.
     * @param string $xml the serialize string to deserialized in the payload
     * @param string $customerOrderId
     * @param string $storeId
     * @param string $orderCreateTimestamp
     * @param array $reason
     * @dataProvider providerDeserialize
     */
    public function testOrderRejectedDeserialize($xml, $customerOrderId, $storeId, $orderCreateTimestamp, $reason, $code)
    {
        $payload = $this->createNewPayload();
        $payload->deserialize($xml);
        $this->assertSame($customerOrderId, $payload->getCustomerOrderId());
        $this->assertSame($storeId, $payload->getStoreId());
        $this->assertSame($orderCreateTimestamp, $payload->getOrderCreateTimestamp()->format('c'));
        $this->assertSame($reason, $payload->getReason());
        $this->assertSame($code, $payload->getCode());
    }
}