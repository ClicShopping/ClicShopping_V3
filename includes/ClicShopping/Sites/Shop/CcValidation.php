<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShopping(Tm) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Sites\Shop;
use function strlen;

class CcValidation
{
  public string $cc_type;
  public string $cc_number;
  public string $cc_expiry_month;
  public string $cc_expiry_year;

  public function __construct(string $number, string $expiry_m, string $expiry_y)
  {
    $this->cc_number = preg_replace('/[^0-9]/', '', $number);

    if (preg_match('/^4[0-9]{12}([0-9]{3})?$/', $this->cc_number)) {
      $this->cc_type = 'Visa';
    } elseif (preg_match('/^5[1-5][0-9]{14}$/', $this->cc_number)) {
      $this->cc_type = 'Master Card';
    } elseif (preg_match('/^3[47][0-9]{13}$/', $this->cc_number)) {
      $this->cc_type = 'American Express';
    } elseif (preg_match('/^3(0[0-5]|[68][0-9])[0-9]{11}$/', $this->cc_number)) {
      $this->cc_type = 'Diners Club';
    } elseif (preg_match('/^6011[0-9]{12}$/', $this->cc_number)) {
      $this->cc_type = 'Discover';
    } elseif (preg_match('/^(3[0-9]{4}|2131|1800)[0-9]{11}$/', $this->cc_number)) {
      $this->cc_type = 'JCB';
    } elseif (preg_match('/^5610[0-9]{12}$/', $this->cc_number)) {
      $this->cc_type = 'Australian BankCard';
    } else {
      return -1;
    }

    if (is_numeric($expiry_m) && ($expiry_m > 0) && ($expiry_m < 13)) {
      $this->cc_expiry_month = $expiry_m;
    } else {
      return -2;
    }

    $current_year = date('Y');
    $expiry_y = substr($current_year, 0, 2) . $expiry_y;
    if (is_numeric($expiry_y) && ($expiry_y >= $current_year) && ($expiry_y <= ($current_year + 10))) {
      $this->cc_expiry_year = $expiry_y;
    } else {
      return -3;
    }

    if ($expiry_y == $current_year) {
      if ($expiry_m < date('n')) {
        return -4;
      }
    }

    return $this->isValid();
  }

  /**
   * @return bool
   */
  public function isValid(): bool
  {
    $cardNumber = strrev($this->cc_number);
    $numSum = 0;
    $cardNumber = strlen($cardNumber);

    for ($i = 0; $i < $cardNumber; $i++) {
      $currentNum = substr($cardNumber, $i, 1);

// Double every second digit
      if ($i % 2 == 1) {
        $currentNum *= 2;
      }

// Add digits of 2-digit numbers together
      if ($currentNum > 9) {
        $firstNum = $currentNum % 10;
        $secondNum = ($currentNum - $firstNum) / 10;
        $currentNum = $firstNum + $secondNum;
      }

      $numSum += $currentNum;
    }

// If the total has no remainder it's OK
    return ($numSum % 10 == 0);
  }
}
