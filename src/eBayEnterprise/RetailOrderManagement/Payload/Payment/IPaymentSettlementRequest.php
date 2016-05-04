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
/**
 * Interface ISettlementCreateRequest
 * @package eBayEnterprise\RetailOrderManagement\Payload\Payment
 */
interface IPaymentSettlementRequest extends IPayload, IOrderId
{
    // XML related values - document root node, XMLNS and name of the xsd schema file
    const XML_NS = 'http://api.gsicommerce.com/schema/checkout/1.0';
    const ROOT_NODE = 'PaymentSettlementRequest';
    const XSD = '/checkout/1.0/Payment-Service-PaymentSettlement-1.0.xsd';

    /**
     * RequestId is used to globally identify a request message and is used
     * for duplicate request protection.
     *
     * xsd restrictions: 1-40 characters
     * @return string
     */
    public function getRequestId();

    /**
     * @param string
     * @return self
     */
    public function setRequestId($requestId);

    /**
     * The amount to capture
     *
     * xsd note: minimum value 0
     *           maximum precision 2 decimal places
     * @return float
     */
    public function getAmount();

    /**
     * @param float
     * @return self
     */
    public function setAmount($amount);

    /**
     * The tax amount to capture
     *
     * xsd note: minimum value 0
     *           maximum precision 2 decimal places
     * @return float
     */
    public function getTaxAmount();

    /**
     * @param float
     * @return self
     */
    public function setTaxAmount($amount);

    /**
     * @return self
     */
    public function getSettlementType();

    /**
     * @param string
     * @return self
     */
    public function setSettlementType($type);

    /**
     * @return self
     */
    public function getClientContext();

    /**
     * @param string
     * @return self
     */
    public function setClientContext($context);

    /**
     * @return self
     */
    public function getFinalDebit();

    /**
     * @param bool
     * @return self
     */
    public function setFinalDebit($finalDebit);
}
