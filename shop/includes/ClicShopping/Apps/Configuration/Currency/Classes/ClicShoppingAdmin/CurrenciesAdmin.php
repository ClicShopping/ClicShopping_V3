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

  namespace ClicShopping\Apps\Configuration\Currency\Classes\ClicShoppingAdmin;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\HTML;

  use ClicShopping\Sites\Shop\Tax;


  class CurrenciesAdmin
  {
    protected $currencies;
    protected $default;
    protected $selected;
    protected $db;

    public function __construct(array $currencies = null)
    {
      $this->db = Registry::get('Db');
      $this->currencies = [];

      $Qcurrencies = $this->db->query('select currencies_id,
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
          'currencies_id' => (int)$c['currencies_id'],
          'title' => $c['title'],
          'symbol_left' => $c['symbol_left'],
          'symbol_right' => $c['symbol_right'],
          'decimal_point' => $c['decimal_point'],
          'thousands_point' => $c['thousands_point'],
          'decimal_places' => (int)$c['decimal_places'],
          'value' => (float)$c['value'],
        ];

        if (!isset($this->default) && ((float)$c['value'] === 1.0)) {
          $this->default = $c['code'];
        }
      }
    }

    /**
     * @param float $number
     * @param string|null $currency_code
     * @param float|null $currency_value
     * @param bool $calculate
     * @return string
     */
    public function show(float $number, string $currency_code = null, float $currency_value = null, bool $calculate = true): string
    {
      if (!isset($currency_code)) {
        $currency_code = $this->getDefault();
      }

      $value = $this->raw($number, $currency_code, $currency_value, $calculate, true);

      return $this->currencies[$currency_code]['symbol_left'] . $value . $this->currencies[$currency_code]['symbol_right'];
    }


    /** @return array|string */
    /**
     * @param string|null $key
     * @param string|null $currency_code
     * @return mixed
     */
    public function get(string $key = null, string $currency_code = null)
    {
      if (!isset($currency_code)) {
        $currency_code = $this->getDefault();
      }

      if (isset($key)) {
        return $this->currencies[$currency_code][$key];
      }

      return $this->currencies[$currency_code];
    }

    /**
     * @param int $id
     * @return string|null
     */
    public function getCode(int $id): ?string
    {
      foreach ($this->currencies as $code => $c) {
        if ($c['currencies_id'] === $id) {
          return $code;
        }
      }

      return null;
    }

    /**
     * @return array
     */
      public function getAll(): array
    {
      $result = [];

      foreach ($this->currencies as $code => $c) {
        $result[] = [
          'code' => $code,
          'title' => $c['title']
        ];
      }

      return $result;
    }

    /**
     * @param float $number
     * @param bool $use_trim
     * @return array
     */
    public function showAll(float $number, bool $use_trim = false): array
    {
      $result = [];

      foreach (array_keys($this->currencies) as $code) {
        $value = $this->show($number, $code);

        if ($use_trim === true) {
          $value = $this->trim($value);
        }

        $result[$code] = $value;
      }

      return $result;
    }

    /**
     * @param bool $true_default
     * @return string|null
     */
    public function getDefault(bool $true_default = false): ?string
    {
      return (($true_default === false) && $this->hasSelected()) ? $this->selected : $this->default;
    }

    /**
     * @return string|null
     */
    public function getSelected(): ?string
    {
      return $this->selected;
    }

    /**
     * @return bool
     */
    public function hasSelected(): bool
    {
      return isset($this->selected);
    }

    /**
     * @param string $code
     * @return bool
     */
    public function setSelected(string $code): bool
    {
      if ($this->exists($code)) {
        $this->selected = $code;

        return true;
      }

      return false;
    }

    /**
     * @param string $code
     * @return bool
     */
    public function exists(string $code): bool
    {
      return array_key_exists($code, $this->currencies);
    }
  }