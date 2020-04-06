<?php

namespace Nets\Easy;

/**
 * @property-read string $addressLine1
 * @property-read string $addressLine2
 * @property-read string $receiverLine
 * @property-read string $postalCode
 * @property-read string $city
 * @property-read string $country
 * @property-read Company $company
 * @property-read ShippingAddress $shippingAddress
 * @property-read PrivatePerson $privatePerson
 */
class Consumer extends EasyType
{
  /** @var array */
  protected $timestamps = [];

  public function getCompanyAttribute($company)
  {
    return new Company($company);
  }

  public function getShippingAddressAttribute($shippingAddress)
  {
    return new ShippingAddress($shippingAddress);
  }

  public function getPrivatePersonAttribute($privatePerson)
  {
    return new PrivatePerson($privatePerson);
  }
}
