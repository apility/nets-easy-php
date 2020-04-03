<?php

namespace Apility\DIBS\Easy;

/**
 * @property-read string $firstName
 * @property-read string $lastName
 * @property-read string $email
 * @property-read PhoneNumber $phoneNumber
 */
class PrivatePerson extends DIBSEasyType
{
  /** @var array */
  protected $timestamps = [];

  public function getPhoneNumberAttribute($phoneNumber)
  {
    return new PhoneNumber($phoneNumber);
  }
}
