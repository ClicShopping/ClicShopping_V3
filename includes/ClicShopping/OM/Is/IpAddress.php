<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\OM\Is;

use UnexpectedValueException;

class IpAddress implements \ClicShopping\OM\IsInterface
{
  public static function execute($value, string $type = 'any'): bool
  {
    if (empty($value)) {
      return false;
    }

    try {
      $options = [];

      if ($type === 'any') {
        $options['flags'] = \FILTER_FLAG_IPV4 | \FILTER_FLAG_IPV6;
      } elseif ($type === 'ipv4') {
        $options['flags'] = \FILTER_FLAG_IPV4;
      } elseif ($type === 'ipv6') {
        $options['flags'] = \FILTER_FLAG_IPV6;
      } else {
        throw new UnexpectedValueException('Invalid type "' . $type . '". Expecting "any", "ipv4", or "ipv6".');
      }

      return filter_var($value, \FILTER_VALIDATE_IP, $options) !== false;
    } catch (UnexpectedValueException $e) {
      trigger_error('ClicShopping\OM\Is\IpAddress: ' . $e->getMessage());
    }

    return false;
  }
}