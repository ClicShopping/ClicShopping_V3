<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Sites\ClicShoppingAdmin;

use ClicShopping\OM\HTML;
use ClicShopping\OM\Registry;
/**
 * Retrieves the name associated with a given product option ID.
 *
 * This function fetches the product option name based on the provided option ID and the language ID
 * obtained from the system's registry.
 *
 * @param int $options_id The ID of the product option.
 * @return string The name of the product option.
 */
class ProductsAttributesAdmin
{

  /**
   * products options - attributes
   *
   * @param string $options_id
   * @return string $values_values['products_options_values_name'], the value of the option name
   *
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
   *
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

  /**
   * Alias function for module configuration keys
   * @param array $select_array
   * @param string $key_name
   * @param string $key_value
   * @return string
   */
  public static function getModSelectOption(array $select_array, string $key_name, string $key_value): string
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