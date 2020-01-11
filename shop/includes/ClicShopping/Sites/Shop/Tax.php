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

  namespace ClicShopping\Sites\Shop;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\CLICSHOPPING;

  class Tax
  {

    protected $tax_rates = [];
    public static $tag;

    /**
     * @param int $class_id
     * @param int $country_id
     * @param int $zone_id
     * @return mixed
     */
    public function getTaxRate(int $class_id, ?int $country_id = -1, ?int $zone_id = -1)
    {
      $CLICSHOPPING_Customer = Registry::get('Customer');
      $CLICSHOPPING_Db = Registry::get('Db');

      if (($country_id == -1) && ($zone_id == -1)) {
        if (!$CLICSHOPPING_Customer->isLoggedOn() || !$CLICSHOPPING_Customer->hasDefaultAddress()) {
          $country_id = STORE_COUNTRY;
          $zone_id = STORE_ZONE;
        } else {
          $country_id = $CLICSHOPPING_Customer->getCountryID();
          $zone_id = $CLICSHOPPING_Customer->getZoneID();
        }
      }

      if (!isset($this->tax_rates[$class_id][$country_id][$zone_id]['rate'])) {
        $Qtax = $CLICSHOPPING_Db->prepare('select sum(tr.tax_rate) as tax_rate
                                            from :table_tax_rates tr left join :table_zones_to_geo_zones za on (tr.tax_zone_id = za.geo_zone_id)
                                                                     left join :table_geo_zones tz on (tz.geo_zone_id = tr.tax_zone_id)
                                            where (za.zone_country_id is null
                                                    or za.zone_country_id = 0
                                                    or za.zone_country_id = :zone_country_id
                                                   )
                                            and (za.zone_id is null
                                                  or za.zone_id = 0
                                                  or za.zone_id = :zone_id
                                                  )
                                            and tr.tax_class_id = :tax_class_id
                                            group by tr.tax_priority
                                           ');
        $Qtax->bindInt(':zone_country_id', $country_id);
        $Qtax->bindInt(':zone_id', $zone_id);
        $Qtax->bindInt(':tax_class_id', $class_id);
        $Qtax->execute();

        if ($Qtax->rowCount() > 0) {
          $tax_multiplier = 1.0;

          do {
            $tax_multiplier *= 1.0 + ($Qtax->valueDecimal('tax_rate') / 100);
          } while ($Qtax->fetch());

          $tax_rate = ($tax_multiplier - 1.0) * 100;
        } else {
          $tax_rate = 0;
        }

        $this->tax_rates[$class_id][$country_id][$zone_id]['rate'] = $tax_rate;
      }

      return $this->tax_rates[$class_id][$country_id][$zone_id]['rate'];
    }

    /**
     * Return the tax description for a zone / class
     * @param int $class_id
     * @param int $country_id
     * @param int $zone_id
     * @return mixed
     */
    public function getTaxRateDescription(int $class_id, ?int $country_id, ?int $zone_id)
    {
      $CLICSHOPPING_Db = Registry::get('Db');

      if (!isset($this->tax_rates[$class_id][$country_id][$zone_id]['description'])) {
        if (DISPLAY_PRICE_WITH_TAX == 'true') {
          $tag = CLICSHOPPING::getDef('text_price_with_tax_tag');
        } else {
          $tag = '';
        }

        $Qtax = $CLICSHOPPING_Db->prepare('select tr.tax_description
                                            from :table_tax_rates tr left join :table_zones_to_geo_zones za on (tr.tax_zone_id = za.geo_zone_id)
                                                                     left join :table_geo_zones tz on (tz.geo_zone_id = tr.tax_zone_id)
                                            where (za.zone_country_id is null or
                                                    za.zone_country_id = 0 or
                                                    za.zone_country_id = :zone_country_id
                                                    )
                                            and (za.zone_id is null or
                                                  za.zone_id = 0 or
                                                  za.zone_id = :zone_id
                                                  )
                                            and tr.tax_class_id = :tax_class_id
                                            order by tr.tax_priority
                                           ');

        $Qtax->bindInt(':zone_country_id', $country_id);
        $Qtax->bindInt(':zone_id', $zone_id);
        $Qtax->bindInt(':tax_class_id', $class_id);
        $Qtax->execute();

        if ($Qtax->rowCount() > 0) {
          $tax_description = '';

          do {
            $tax_description .= $Qtax->value('tax_description') . ' x ';
          } while ($Qtax->fetch());

          $this->tax_rates[$class_id][$country_id][$zone_id]['description'] = $tag . substr($tax_description, 0, -3);
        } else {
          $this->tax_rates[$class_id][$country_id][$zone_id]['description'] = $tag . CLICSHOPPING::getDef('text_unknown_tax_rate');
        }
      }

      return $this->tax_rates[$class_id][$country_id][$zone_id]['description'];
    }

    /**
     * @param float $price
     * @param float $tax_rate
     * @return float
     */
    public static function calculate(float $price, float $tax_rate): float
    {
      $CLICSHOPPING_Currencies = Registry::get('Currencies');

      return round($price * $tax_rate / 100, $CLICSHOPPING_Currencies->currencies[DEFAULT_CURRENCY]['decimal_places']);
    }

    /**
     * @param float $value
     * @param string|null $padding
     * @return string
     */
    public static function displayTaxRateValue(float $value, string $padding = null): string
    {
      if (!is_numeric($padding)) {
        $padding = TAX_DECIMAL_PLACES;
      }

      if (strpos($value, '.') !== false) {
        while (true) {
          if (substr($value, -1) == '0') {
            $value = substr($value, 0, -1);
          } else {
            if (substr($value, -1) == '.') {
              $value = substr($value, 0, -1);
            }

            break;
          }
        }
      }

      if ($padding > 0) {
        if (($decimal_pos = strpos($value, '.')) !== false) {
          $decimals = strlen(substr($value, ($decimal_pos + 1)));

          for ($i = $decimals; $i < $padding; $i++) {
            $value .= '0';
          }
        } else {
          $value .= '.';

          for ($i = 0; $i < $padding; $i++) {
            $value .= '0';
          }
        }
      }

      return $value . '%';
    }

    /**
     * Add tax to a products price
     * symbol tax :display information after currency (ex : HT / TTC)
     * @param $price
     * @param $tax
     */
    public static function addTax(float $price, float $tax)
    {
      $CLICSHOPPING_Customer = Registry::get('Customer');
      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_Currencies = Registry::get('Currencies');

      $QgroupTax = $CLICSHOPPING_Db->prepare('select group_tax
                                              from :table_customers_groups
                                              where customers_group_id = :customers_group_id
                                              ');
      $QgroupTax->bindInt(':customers_group_id', $CLICSHOPPING_Customer->getCustomersGroupID());

      $QgroupTax->execute();

      $group_tax = $QgroupTax->fetch();

// Code modifie par rapport a l'original afin d'avoir un meilleur controle sur l'affichage de la TVA selon les comptes clients
      if (($CLICSHOPPING_Customer->isLoggedOn()) && ($CLICSHOPPING_Customer->getCustomersGroupID() != 0) && ($group_tax['group_tax'] == 'true') && ($tax > 0)) {
        $group_taxed = 'true';
      } else if (($CLICSHOPPING_Customer->isLoggedOn()) && ($CLICSHOPPING_Customer->getCustomersGroupID() != 0) && ($group_tax['group_tax'] != 'true')) {
        $group_taxed = 'false';
      } else if (($CLICSHOPPING_Customer->isLoggedOn()) && (DISPLAY_PRICE_WITH_TAX == 'true') && ($tax > 0)) {
        $group_taxed = 'true';
      } else if ((!$CLICSHOPPING_Customer->isLoggedOn()) && (DISPLAY_PRICE_WITH_TAX == 'true') && ($tax > 0)) {
        $group_taxed = 'true';
      } else {
        $group_taxed = 'false';
      }

      switch ($group_taxed) {
        case 'true':
          static::$tag = CLICSHOPPING::getDef('tax_included');
          return round($price, $CLICSHOPPING_Currencies->currencies[DEFAULT_CURRENCY]['decimal_places']) + static::calculate($price, $tax);
          break;
        case 'false':
          static::$tag = CLICSHOPPING::getDef('tax_excluded');
          return round($price, $CLICSHOPPING_Currencies->currencies[DEFAULT_CURRENCY]['decimal_places']);
          break;
        default:
          static::$tag = CLICSHOPPING::getDef('tax_excluded');
          return round($price, $CLICSHOPPING_Currencies->currencies[DEFAULT_CURRENCY]['decimal_places']);
          break;
      }
    }

    /**
     * taxClassDropDown
     *
     * @return string $$tax_class_array, drop down with all tax title
     * @access public
     */

    public static function taxClassDropDown(): array
    {
      $CLICSHOPPING_Db = Registry::get('Db');

      $tax_class_array = array(array('id' => '0',
        'text' => CLICSHOPPING::getDef('text_none')
      )
      );

      $QtaxClass = $CLICSHOPPING_Db->get('tax_class', [
        'tax_class_id',
        'tax_class_title'
      ],
        null,
        'tax_class_title'
      );

      while ($QtaxClass->fetch()) {
        $tax_class_array[] = ['id' => $QtaxClass->valueInt('tax_class_id'),
          'text' => $QtaxClass->value('tax_class_title')
        ];
      }

      return $tax_class_array;
    }

    /**
     * @return string
     */
    public function getTag(): string
    {
      $tag = static::$tag;

      return $tag;
    }
  }
