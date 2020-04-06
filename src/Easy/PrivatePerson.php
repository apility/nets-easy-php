<?php

namespace Nets\Easy;

/**
 * @property-read string $firstName
 * @property-read string $lastName
 * @property-read string $email
 * @property-read PhoneNumber $phoneNumber
 */
class PrivatePerson extends EasyType
{
  /** @var array */
  protected $timestamps = [];

  public function getPhoneNumberAttribute($phoneNumber)
  {
    return new PhoneNumber($phoneNumber);
  }
}
