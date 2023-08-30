<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShopping(Tm) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

use ClicShopping\OM\HTML;

use ClicShopping\Apps\Configuration\ProductsLength\Classes\Shop\ProductsLength;

function clic_cfg_set_products_lenght_classes_pulldown_menu($default, $key = null)
{
  $name = (empty($key)) ? 'configuration_value' : 'configuration[' . $key . ']';

  $weight_class_array = [];

  foreach (ProductsLength::getClasses() as $class) {
    $weight_class_array[] = ['id' => $class['id'],
      'text' => $class['title']
    ];
  }

  return HTML::selectMenu($name, $weight_class_array, $default);
}