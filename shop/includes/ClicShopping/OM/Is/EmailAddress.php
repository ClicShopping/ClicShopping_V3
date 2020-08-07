<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @licence MIT - Portion of osCommerce 2.4
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\OM\Is;

  class EmailAddress implements \ClicShopping\OM\IsInterface
  {
    public static function execute($value, bool $check_dns = false): bool
    {
      if (empty($value) || (strlen($value) > 191)) {
        return false;
      }

      if (filter_var($value, \FILTER_VALIDATE_EMAIL) === false) {
        return false;
      }

      if ($check_dns === true || ENTRY_EMAIL_ADDRESS_CHECK == 'true') {
        $domain = explode('@', $value, 2);

        // international domains (eg, containing german umlauts) are converted to punycode
        if (mb_detect_encoding($domain[1], 'ASCII', true) !== 'ASCII') {
            $domain[1] = idn_to_ascii($domain[1]);
        }

        if ($domain[1] === false) {
            return false;
        }

        if (!checkdnsrr($domain[1], 'MX') && !checkdnsrr($domain[1], 'A')) {
            return false;
        }
      }

      return true;
    }
  }
