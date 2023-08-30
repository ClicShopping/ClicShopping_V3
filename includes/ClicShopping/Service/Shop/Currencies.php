<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShopping(Tm) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Service\Shop;

use ClicShopping\OM\CLICSHOPPING;
use ClicShopping\OM\HTML;
use ClicShopping\OM\Registry;

use ClicShopping\Apps\Configuration\Currency\Classes\Shop\Currencies as CurrenciesClass;

class Currencies implements \ClicShopping\OM\ServiceInterface
{
  public static function start(): bool
  {
    if (is_file(CLICSHOPPING::BASE_DIR . 'Apps/Configuration/Currency/Classes/Shop/Currencies.php')) {
      Registry::set('Currencies', new CurrenciesClass());
      $CLICSHOPPING_Currencies = Registry::get('Currencies');

      if (!isset($_SESSION['currency']) || isset($_GET['currency']) || ((USE_DEFAULT_LANGUAGE_CURRENCY == 'true') && (CLICSHOPPING::getDef('language_currency') != $_SESSION['currency']))) {
        if (isset($_GET['currency']) && $CLICSHOPPING_Currencies->isSet($_GET['currency'])) {
          $_SESSION['currency'] = HTML::sanitize($_GET['currency']);
        } else {
          $_SESSION['currency'] = ((USE_DEFAULT_LANGUAGE_CURRENCY == 'true') && $CLICSHOPPING_Currencies->isSet(CLICSHOPPING::getDef('language_currency'))) ? CLICSHOPPING::getDef('language_currency') : DEFAULT_CURRENCY;
        }
      }
      return true;
    } else {
      return false;
    }
  }

  public static function stop(): bool
  {
    return true;
  }
}
