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

  namespace ClicShopping\Sites\ClicShoppingAdmin;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;

  class Tax extends \ClicShopping\Sites\Shop\Tax
  {
    public function getTaxRate($class_id, $country_id = null, $zone_id = null)
    {
      if (!isset($country_id) && !isset($zone_id)) {
        $country_id = HTML::sanitize(STORE_COUNTRY);
        $zone_id = HTML::sanitize(STORE_ZONE);
      }

      return parent::getTaxRate($class_id, $country_id, $zone_id);
    }

    public static function getClasses(): array
    {
      $CLICSHOPPING_Db = Registry::get('Db');

      $Qtc = $CLICSHOPPING_Db->query('select tax_class_id,
                                             tax_class_title
                                      from :table_tax_class
                                      order by tax_class_title
                                    ');
      $Qtc->execute();

      return $Qtc->fetchAll();
    }

// Add tax to a products price
    public static function addTax($price, $tax, $override = false)
    {
      if (((DISPLAY_PRICE_WITH_TAX == 'true') || ($override === true)) && ($tax > 0)) {
        return $price + parent::calculate($price, $tax);
      } else {
        return $price;
      }
    }

    /**
     * Drop down of the class title
     *
     * @access public
     * @param string $parameters , $selected
     * @return string $select_string, the drop down f the title class
     *
     */
    public static function getTaxClassesPullDown($parameters, $selected = '')
    {
      $select_string = '<select ' . $parameters . '>';

      $Qclasses = Registry::get('Db')->get('tax_class', [
        'tax_class_id',
        'tax_class_title'
      ],
        null,
        'tax_class_title'
      );

      while ($Qclasses->fetch()) {
        $select_string .= '<option value="' . $Qclasses->valueInt('tax_class_id') . '"';

        if ($selected == $Qclasses->valueInt('tax_class_id')) {
          $select_string .= ' SELECTED';
        }

        $select_string .= '>' . $Qclasses->value('tax_class_title') . '</option>';
      }

      $select_string .= '</select>';

      return $select_string;
    }

    /**
     * return value of taxe
     *
     * @access public
     * @param string $class_id , class id
     * @return string  value of the taxe
     *
     */
    public function getTaxRateValue(int $class_id): string
    {
      return self::getTaxRate($class_id, -1, -1);
    }

    /**
     * return drop down
     *
     */
    public static function taxClassDropDown(): array
    {
      return parent::taxClassDropDown();
    }

    public static function displayTaxRateValue(float $value, string $padding = null): string
    {
      return parent::displayTaxRateValue($value, $padding);
    }
  }
