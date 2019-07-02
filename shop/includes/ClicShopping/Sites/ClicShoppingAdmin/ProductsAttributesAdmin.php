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

  class ProductsAttributesAdmin
  {

    /**
     * products options - attributes
     *
     * @param string $options_id
     * @return string $values_values['products_options_values_name'], the value of the option name
     * @access public
     */
    public static function getOptionsName(int $options_id): string
    {
      $CLICSHOPPING_Language = Registry::get('Language');

      $Qoptions = Registry::get('Db')->get('products_options', 'products_options_name', ['products_options_id' => (int)$options_id,
          'language_id' => (int)$CLICSHOPPING_Language->getId()
        ]
      );

      return $Qoptions->value('products_options_name');

    }


    /**
     * products options name - attributes
     *
     * @param string $values_id
     * @return string $values_values['products_options_values_name'], the name value of the option name
     * @access public
     */
    public static function getValuesName(int $values_id): string
    {
      $CLICSHOPPING_Language = Registry::get('Language');
      $Qvalues = Registry::get('Db')->get('products_options_values', 'products_options_values_name', ['products_options_values_id' => (int)$values_id,
          'language_id' => (int)$CLICSHOPPING_Language->getId()
        ]
      );

      return $Qvalues->value('products_options_values_name');
    }

////
// Alias function for module configuration keys
//atributes
    public static function getModSelectOption(string $select_array, string $key_name, string $key_value): string
    {
      $string = '';

      foreach ($select_array as $key => $value) {
        if (is_int($key)) $key = $value;
        $string .= '<br />' . HTML::radioField('configuration[' . $key_name . ']', $key);
        if ($key_value == $key) $string .= ' checked="checked"';
        $string .= ' /> ' . $value;
      }

      return $string;
    }
  }