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

use eBayEnterprise\RetailOrderManagement\Payload\IPayload;
use eBayEnterprise\RetailOrderManagement\Payload\OrderEvents\IOrderEvent;

/**
 * Interface IRiskAssessmentReply
 * @package eBayEnterprise\RetailOrderManagement\Payload\Risk
 */
interface IRiskAssessmentReply extends IPayload, IOrderEvent
{
    // XML related values - document root node, XMLNS and name of the xsd schema file
    const XML_NS = 'http://api.gsicommerce.com/schema/checkout/1.0';
    const ROOT_NODE = 'RiskAssessmentReply';
    const XSD = '/checkout/1.0/Risk-Service-RiskAssessment-1.0.xsd';

    public function getResponseCode();
    public function setResponseCode($responseCode);
}
