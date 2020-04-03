<?php

namespace Apility\DIBS\Easy;

/**
 * @property-read ContactDetails $contactDetails
 */
class Company extends DIBSEasyType
{
  /** @var array */
  protected $timestamps = [];

  public function getContactDetailsAttribute($contactDetails)
  {
    return new ContactDetails($contactDetails);
  }
}
