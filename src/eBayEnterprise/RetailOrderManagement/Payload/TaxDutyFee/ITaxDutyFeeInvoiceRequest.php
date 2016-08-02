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
use DateTime;

interface ITaxDutyFeeInvoiceRequest extends ITaxDutyFeeInvoice, IShipGroupContainer, IDestinationContainer, ITaxedFeeContainer
{
    const XSD = '/checkout/1.0/TaxDutyFee-InvoiceRequest-1.0.xsd';
    const ROOT_NODE = 'TaxInvoiceRequest';

    /**
     * Order ID For Tax Invoice
     * 
     * @return string
     */
    public function getOrderId();

    /**
     * @param string
     * @return self 
     */
    public function setOrderId($orderId);

    /**
     * Invoice Number for Tax Invoice
     *
     * @return string
     */
    public function getInvoiceNumber();

    /**
     * @param string
     * @return self
     */
    public function setInvoiceNumber($invoiceNumber);

    /**
     * Order Date Time
     *
     * @return \DateTime
     */
    public function getOrderDateTime();

    /**
     * @param \DateTime
     * @return self
     */
    public function setOrderDateTime(DateTime $orderDateTime);

    /**
     * Ship Date Time
     *
     * @return \DateTime
     */
    public function getShipDateTime();

    /**
     * @param \DateTime
     * @return self
     */
    public function setShipDateTime(DateTime $shipDateTime);

    /**
     * Currency code for the request.
     *
     * Must conform to ISO 4217:2008
     * @link http://en.wikipedia.org/wiki/ISO_4217
     *
     * restrictions: 2 >= length <= 40
     * @return string
     */
    public function getCurrency();

    /**
     * @param string
     * @return self
     */
    public function setCurrency($currency);

    /**
     * Flag indicating prices already have VAT tax included.
     *
     * restrictions: optional
     * @return bool
     */
    public function getVatInclusivePricingFlag();

    /**
     * @param bool
     * @return self
     */
    public function setVatInclusivePricingFlag($flag);

    /**
     * Customer billing address
     *
     * @return IDestination
     */
    public function getBillingInformation();

    /**
     * @param IDestination
     * @return self
     */
    public function setBillingInformation(IDestination $billingDest);
}
