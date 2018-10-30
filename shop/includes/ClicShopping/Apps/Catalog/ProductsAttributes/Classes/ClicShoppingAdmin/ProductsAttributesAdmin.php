<?php
/**
 *
 *  @copyright 2008 - https://www.clicshopping.org
 *  @Brand : ClicShopping(Tm) at Inpi all right Reserved
 *  @Licence GPL 2 & MIT
 *  @licence MIT - Portion of osCommerce 2.4
 *
 *
 */

  namespace ClicShopping\Apps\Catalog\ProductsAttributes\Classes\ClicShoppingAdmin;

  use ClicShopping\OM\Registry;

  class ProductsAttributesAdmin {
    protected $lang;

    public function __construct() {
      $this->lang = Registry::get('Language');
    }


/**
 * products options - attributes
 *
 * @param string $options_id
 * @return string $values_values['products_options_values_name'], the value of the option name
 * @access public
 */
    public function getOptionsName($options_id) {
      $Qoptions = Registry::get('Db')->get('products_options', 'products_options_name', ['products_options_id' => (int)$options_id,
                                                                                         'language_id' => (int)$this->lang->getId()
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
    public function getValuesName($values_id) {
      $Qvalues = Registry::get('Db')->get('products_options_values', 'products_options_values_name', ['products_options_values_id' => (int)$values_id,
                                                                                                      'language_id' => (int)$this->lang->getId()
                                                                                                     ]
                                          );

      return $Qvalues->value('products_options_values_name');
    }
  }