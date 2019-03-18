<?php
/**
 *
 *  @copyright 2008 - https://www.clicshopping.org
 *  @Brand : ClicShopping(Tm) at Inpi all right Reserved
 *  @Licence GPL 2 & MIT
 *  @licence MIT - Portion of osCommerce 2.4
 *  @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

  namespace ClicShopping\Apps\Configuration\Currency\Sites\ClicShoppingAdmin\Pages\Home\Actions\Currency;

  use ClicShopping\Apps\Configuration\Currency\Lib\PHPXurrency;
  use ClicShopping\OM\Registry;

  class UpdateAll extends \ClicShopping\OM\PagesActionsAbstract {
    protected $app;

    public function __construct() {
      $this->app = Registry::get('Currency');
    }

    public function getConvertCurrency() {
      $api_id = CLICSHOPPING_APP_CURRENCY_CR_API_KEY;

      $url = 'https://openexchangerates.org/api/latest.json?app_id=' . $api_id;
      $useragent = 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:37.0) Gecko/20100101 Firefox/37.0';
      $rawdata = '';

      if (function_exists('curl_exec')) {
        $conn = curl_init($url);
        curl_setopt($conn, CURLOPT_USERAGENT, $useragent);
        curl_setopt($conn, CURLOPT_FRESH_CONNECT, true);
        curl_setopt($conn, CURLOPT_RETURNTRANSFER, true);
        $rawdata = curl_exec($conn);
        curl_close($conn);
      } else {
        $options = array('http' => array('user_agent' => $useragent));
        $context = stream_context_create($options);
        if (function_exists('file_get_contents')) {
          $rawdata = file_get_contents($url, false, $context);
        } else if (function_exists('fopen') && function_exists('stream_get_contents')) {
          $handle = fopen($url, "r", false, $context);
          if ($handle) {
            $rawdata = stream_get_contents($handle);
            fclose($handle);
          }
        }
      }

      $open_exchange = json_decode($rawdata);

      if ($open_exchange->error === false) {
        $all_rates = $open_exchange->rates;

        return $all_rates;
      } else {
        return false;
      }
    }

    public function execute() {
      $Qcurrency = $this->app->db->prepare('select currencies_id,
                                                   code,
                                                   title
                                            from :table_currencies
                                          ');
      $Qcurrency->bindInt(':currencies_id', $_GET['cID']);
      $Qcurrency->execute();

      while ($Qcurrency->fetch()) {
        $code = $Qcurrency->value('code');

        $rate = $this->getConvertCurrency();

        if ($rate !== false) {
          if (DEFAULT_CURRENCY == 'USD') {
            $toCurrency = $rate->USD;
            $fromCurrency = $rate->$code;
            $rate = $toCurrency * (1 / $fromCurrency);
          } else if (DEFAULT_CURRENCY != 'USD') {
            $default = DEFAULT_CURRENCY;
            $toCurrency = $rate->$default;
            $fromCurrency = $rate->$code;

            $rate = $toCurrency * (1 / $fromCurrency);
          }

          if ($rate == 0) $rate = 1;

          $this->app->db->save('currencies', [
                                              'value' => $rate,
                                              'last_updated' => 'now()'
                                              ], [
                                                'currencies_id' => $Qcurrency->valueInt('currencies_id')
                                              ]
                              );
        }
      }

      $this->app->redirect('Currency&page=' . $_GET['page'] . '&cID=' . $Qcurrency->valueInt('currencies_id'));
    }
  }