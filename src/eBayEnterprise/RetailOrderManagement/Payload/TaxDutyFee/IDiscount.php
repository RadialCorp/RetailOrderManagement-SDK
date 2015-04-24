<?php
/**
 * Copyright (c) 2014-2015 eBay Enterprise, Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @copyright   Copyright (c) 2014-2015 eBay Enterprise, Inc. (http://www.ebayenterprise.com/)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace eBayEnterprise\RetailOrderManagement\Payload\TaxDutyFee;

use eBayEnterprise\RetailOrderManagement\Payload\IPayload;

interface IDiscount extends IPayload
{
    const XML_NS = 'http://api.gsicommerce.com/schema/checkout/1.0';

    /**
     * Unique id for the promotion. Typically generated by the webstore.
     *
     * restrictions: optional
     * @return string
     */
    public function getId();

    /**
     * @param string
     * @return self
     */
    public function setId($id);

    /**
     * Calculated currency amount of the discount.
     *
     * restrictions: two decimal, non-negative
     * @return float
     */
    public function getAmount();

    /**
     * @param float
     * @return self
     */
    public function setAmount($amount);

    /**
     * Whether duty needs to be calculated for the discount. It will be ignored
     * for all other line items except 'Merchandise' and 'Shipping', (Cost
     * Including Freight calculation only). If overall duty is not present in
     * input, then this flag will be ignored and duty will be calculated for
     * all available discounts.
     *
     * restrictions: optional
     * @return bool
     */
    public function getCalculateDutyFlag();

    /**
     * @param bool
     * @return self
     */
    public function setCalculateDutyFlag($flag);
}
