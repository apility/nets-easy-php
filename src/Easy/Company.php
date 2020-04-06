<?php

namespace Nets\Easy;

/**
 * @property-read ContactDetails $contactDetails
 */
class Company extends EasyType
{
  /** @var array */
  protected $timestamps = [];

  public function getContactDetailsAttribute($contactDetails)
  {
    return new ContactDetails($contactDetails);
  }
}
