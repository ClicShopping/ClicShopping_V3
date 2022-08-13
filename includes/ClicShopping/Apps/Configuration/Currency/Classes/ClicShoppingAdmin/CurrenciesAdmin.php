<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\Apps\Configuration\Currency\Classes\ClicShoppingAdmin;

  use ClicShopping\OM\Registry;

  class CurrenciesAdmin extends \ClicShopping\Apps\Configuration\Currency\Classes\Shop\Currencies
  {
    protected mixed $db;

    public function __construct(array $currencies = null)
    {
      $this->db = Registry::get('Db');
      $this->currencies = [];

      $Qcurrencies = $this->db->query('select currencies_id as id,
                                              code,
                                              title,
                                              symbol_left,
                                              symbol_right,
                                              decimal_point,
                                              thousands_point,
                                              decimal_places,
                                              value
                                       from :table_currencies
                                      ');

      $Qcurrencies->execute();

      $currencies = $Qcurrencies->fetchAll();

      foreach ($currencies as $c) {
        $this->currencies[$c['code']] = [
          'id' => (int)$c['id'],
          'title' => $c['title'],
          'symbol_left' => $c['symbol_left'],
          'symbol_right' => $c['symbol_right'],
          'decimal_point' => $c['decimal_point'],
          'thousands_point' => $c['thousands_point'],
          'decimal_places' => (int)$c['decimal_places'],
          'value' => (float)$c['value']
        ];

        if (!isset($this->default) && ((float)$c['value'] === 1.0)) {
          $this->default = $c['code'];
        }
      }
    }

    /**
     * @return array
     */
    public function getAll(): array
    {
      $result = [];

      foreach ($this->currencies as $code => $c) {
        $result[] = [
          'id' => $code,
          'text' => $c['title']
        ];
      }

      return $result;
    }
  }