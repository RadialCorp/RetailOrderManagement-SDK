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

use eBayEnterprise\RetailOrderManagement\Payload\IValidatorIterator;
use eBayEnterprise\RetailOrderManagement\Payload\TPayload;

class MailingAddress implements IMailingAddress
{
    use TPayload, TPhysicalAddress, TPersonName;

    /**
     * @param IValidatorIterator
     */
    public function __construct(IValidatorIterator $validators)
    {
        $this->extractionPaths = [
            'firstName' => 'string(x:PersonName/x:FirstName)',
            'lastName' => 'string(x:PersonName/x:LastName)',
            'city' => 'string(x:Address/x:City)',
            'countryCode' => 'string(x:Address/x:CountryCode)',
        ];
        $this->optionalExtractionPaths = [
            'middleName' => 'x:PersonName/x:MiddleName',
            'honorificName' => 'x:PersonName/x:Honorific',
            'mainDivision' => 'x:Address/x:MainDivision',
            'postalCode' => 'x:Address/x:PostalCode',
        ];
        $this->addressLinesExtractionMap = [
            [
                'property' => 'lines',
                'xPath' => 'x:Address/*[starts-with(name(), "Line")]'
            ],
        ];
        $this->validators = $validators;
    }

    protected function serializeContents()
    {
        return $this->serializePersonName() . $this->serializePhysicalAddress();
    }

    protected function getRootNodeName()
    {
        return static::ROOT_NODE;
    }

    protected function getPersonNameRootNodeName()
    {
        return static::PERSON_NAME_ROOT_NODE;
    }
    protected function getPhysicalAddressRootNodeName()
    {
        return static::PHYSICAL_ADDRESS_ROOT_NODE;
    }

    protected function getXmlNamespace()
    {
        return self::XML_NS;
    }
}