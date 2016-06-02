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
use eBayEnterprise\RetailOrderManagement\Payload\Payment\IPaymentContext;

interface ISettlementStatus extends IPayload, IOrderEvent, IPaymentContext
{
    const EVENT_TYPE = 'settlement_status';
    const ROOT_NODE = 'SettlementStatus';
    const XML_NS = 'http://api.gsicommerce.com/schema/checkout/1.0';
    const XSD = '/checkout/1.0/Payment-Service-PaymentSettlement-1.0.xsd';

    public function getTenderType();

    public function setTenderType($value);

    public function getAmount();

    public function setAmount($value);

    public function getSettlementType();

    public function setSettlementType($value);

    public function getSettlementStatus();

    public function setSettlementStatus($value);

    public function getDeclineReason();

    public function setDeclineReason($value);

    public function getCurrencyCode();

    public function setCurrencyCode($value);

    public function getRequestId();

    public function setRequestId($value);
}
