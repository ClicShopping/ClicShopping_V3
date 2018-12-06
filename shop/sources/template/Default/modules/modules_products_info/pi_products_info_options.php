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

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;
  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\Sites\Shop\Tax;

  class pi_products_info_options {
    public $code;
    public $group;
    public $title;
    public $description;
    public $sort_order;
    public $enabled = false;

    public function __construct() {
      $this->code = get_class($this);
      $this->group = basename(__DIR__);

      $this->title = CLICSHOPPING::getDef('module_products_info_options');
      $this->description = CLICSHOPPING::getDef('module_products_info_options_description');

      if (defined('MODULE_PRODUCTS_INFO_OPTIONS_STATUS')) {
        $this->sort_order = MODULE_PRODUCTS_INFO_OPTIONS_SORT_ORDER;
        $this->enabled = (MODULE_PRODUCTS_INFO_OPTIONS_STATUS == 'True');
      }
    }

    public function execute() {
      $CLICSHOPPING_Tax = Registry::get('Tax');
      $CLICSHOPPING_Category = Registry::get('Category');

      if (isset($_GET['products_id']) && isset($_GET['Products']) ) {

        $content_width = (int)MODULE_PRODUCTS_INFO_OPTIONS_CONTENT_WIDTH;
        $text_position = MODULE_PRODUCTS_INFO_OPTIONS_POSITION;

        $CLICSHOPPING_ProductsCommon = Registry::get('ProductsCommon');
        $CLICSHOPPING_Customer = Registry::get('Customer');
        $CLICSHOPPING_Db = Registry::get('Db');
        $CLICSHOPPING_Template = Registry::get('Template');
        $CLICSHOPPING_Currencies = Registry::get('Currencies');
        $CLICSHOPPING_ShoppingCart = Registry::get('ShoppingCart');
        $CLICSHOPPING_Language = Registry::get('Language');
        $CLICSHOPPING_ProductsAttributes = Registry::get('ProductsAttributes');

          if ($CLICSHOPPING_ProductsAttributes->getCountProductsAttributes() > 0) {
            if ($CLICSHOPPING_Customer->getCustomersGroupID() != 0) {
              $QproductsOptionsName = $CLICSHOPPING_Db->prepare('select distinct popt.products_options_id,
                                                                                  popt.products_options_name,
                                                                                  popt.products_options_type
                                                                      from :table_products_options popt,
                                                                           :table_products_attributes patrib
                                                                      where patrib.products_id= :products_id
                                                                      and patrib.options_id = popt.products_options_id
                                                                      and popt.language_id = :language_id
                                                                      and (patrib.customers_group_id = :customers_group_id or patrib.customers_group_id = 99)
                                                                      order by popt.products_options_sort_order,
                                                                               popt.products_options_name
                                                                     ');
              $QproductsOptionsName->bindInt(':products_id', (int)$CLICSHOPPING_ProductsCommon->getID());
              $QproductsOptionsName->bindInt(':language_id', (int)$CLICSHOPPING_Language->getId());
              $QproductsOptionsName->bindInt(':customers_group_id', $CLICSHOPPING_Customer->getCustomersGroupID());

              $QproductsOptionsName->execute();
            } else {
              $QproductsOptionsName = $CLICSHOPPING_Db->prepare('select distinct popt.products_options_id,
                                                                                  popt.products_options_name,
                                                                                  popt.products_options_type
                                                                      from :table_products_options popt,
                                                                           :table_products_attributes patrib
                                                                      where patrib.products_id= :products_id
                                                                      and patrib.options_id = popt.products_options_id
                                                                      and popt.language_id = :language_id
                                                                      and (patrib.customers_group_id = 0 or patrib.customers_group_id = 99)
                                                                      order by popt.products_options_sort_order,
                                                                               popt.products_options_name
                                                                     ');
              $QproductsOptionsName->bindInt(':products_id', (int)$CLICSHOPPING_ProductsCommon->getID());
              $QproductsOptionsName->bindInt(':language_id', (int)$CLICSHOPPING_Language->getId());

              $QproductsOptionsName->execute();
            }

            $products_options_content_display = '<!-- Start products_options -->' . "\n";
//*****************************
// Strong relations with pi_products_info price.php Don't delete
//*****************************
            if (defined('MODULE_PRODUCTS_INFO_PRICE_SORT_ORDER')) {
                if (MODULE_PRODUCTS_INFO_PRICE_SORT_ORDER > MODULE_PRODUCTS_INFO_OPTIONS_SORT_ORDER) {
                 $products_options_content_display .=  HTML::form('cart_quantity', CLICSHOPPING::link(null, 'Cart&Add&cPath=' . $CLICSHOPPING_Category->getPath(), ' SSL'), 'post', '', ['tokenize' => true]);

               }
            }

            $products_options_content_display .= '<div class="contentText ' . MODULE_PRODUCTS_INFO_OPTIONS_POSITION .';">';
            $products_options_content_display .='<div class="separator"></div>';
            $products_options_content_display .= '<div class="ModuleProductsInfoPositionOption">';
            $products_options_content_display .= '<span class="ModuleProductsInfoOptionsText"><h3>' . CLICSHOPPING::getDef('text_product_options') . '</h3></span>';

            while ($QproductsOptionsName->fetch() ) {

              $products_options_array = [];

              $QproductsOptions = $CLICSHOPPING_ProductsAttributes->getProductsAttributesInfo($CLICSHOPPING_ProductsCommon->getID(), $QproductsOptionsName->valueInt('products_options_id'), null, $CLICSHOPPING_Language->getId());

              if ($QproductsOptionsName->value('products_options_type') == 'select') {

                while ($QproductsOptions->fetch() !== false) {

                  $products_options_array[] = ['id' => $QproductsOptions->valueInt('products_options_values_id'),
                                                'text' => $QproductsOptions->value('products_options_values_name')
                                              ];
                  $products_options_array_id[] = $QproductsOptions->valueInt('products_options_values_id');
                  $products_options_array_name[] = $QproductsOptions->value('products_options_values_name');

                  if ($QproductsOptions->valueDecimal('options_values_price') != '0') {
                    $option_price_display = ' (' . $QproductsOptions->value('price_prefix') . $CLICSHOPPING_Currencies->display_price($QproductsOptions->valueDecimal('options_values_price'), $CLICSHOPPING_Tax->getTaxRate( $CLICSHOPPING_ProductsCommon->getProductsTaxClassId() )) .') ';

                    if (PRICES_LOGGED_IN == 'False') {
                      $option_price_display_d = $option_price_display;
                    }

                    if ((PRICES_LOGGED_IN == 'True') && (!$CLICSHOPPING_Customer->isLoggedOn())) {
                      $option_price_display_d = '';
                    } else {
                      $option_price_display_d = $option_price_display;
                    }

                    $products_options_array[count($products_options_array)-1]['text'] .= $option_price_display_d;
                  }
                } // end while $products_options

                if (is_string($_GET['products_id']) &&  isset($CLICSHOPPING_ShoppingCart->contents[(int)$CLICSHOPPING_ProductsCommon->getID()]['attributes'][$QproductsOptionsName->valueInt('products_options_id')]))  {
                  $selected_attribute = $CLICSHOPPING_ShoppingCart->contents[(int)$CLICSHOPPING_ProductsCommon->getID()]['attributes'][$QproductsOptionsName->valueInt('products_options_id')];
                } else {
                  $selected_attribute = false;
                }

                $products_options_content_display .='<div>';
                $products_options_content_display .='<label class="ModuleProductsInfoOptionsName">'. $QproductsOptionsName->value('products_options_name') . ' : </label>';
                $products_options_content_display .='<span class="ModuleProductsInfoOptionsPullDownMenu">' . HTML::selectMenu('id[' . $QproductsOptionsName->valueInt('products_options_id') . ']', $products_options_array, $selected_attribute, 'class="ModuleProductsInfoOptionsPullDownMenuOptionsInside"') . '</span>';
                $products_options_content_display .='</div>';
                $products_options_content_display .='<div class="separator"></div>';
              } else {
                while ($QproductsOptions->fetch() !== false) {
                  $products_options_array[] = ['id' => $QproductsOptions->valueInt('products_options_values_id'),
                                               'text' => $QproductsOptions->value('products_options_values_name')
                                              ];


                  if ($QproductsOptions->valueDecimal('options_values_price') != '0') {
                    $option_price_display = ' (' . $QproductsOptions->value('price_prefix') . $CLICSHOPPING_Currencies->display_price($QproductsOptions->valueDecimal('options_values_price'), $CLICSHOPPING_Tax->getTaxRate( $CLICSHOPPING_ProductsCommon->getProductsTaxClassId() )) .') ';

                    if (PRICES_LOGGED_IN == 'False') {
                      $option_price_display_d = $option_price_display;
                    }

                    if ((PRICES_LOGGED_IN == 'True') && (!$CLICSHOPPING_Customer->isLoggedOn())) {
                      $option_price_display_d = '';
                    } else {
                      $option_price_display_d = $option_price_display;
                    }

                    $products_options_array[count($products_options_array)-1]['text'] .= $option_price_display_d;
                  }
                } // end while $products_options

                if (is_string($_GET['products_id']) &&  isset($CLICSHOPPING_ShoppingCart->contents[(int)$CLICSHOPPING_ProductsCommon->getID()]['attributes'][$QproductsOptionsName->valueInt('products_options_id')]))  {
                  $selected_attribute = $CLICSHOPPING_ShoppingCart->contents[(int)$CLICSHOPPING_ProductsCommon->getID()]['attributes'][$QproductsOptionsName->valueInt('products_options_id')];
                } else {
                  $selected_attribute = false;
                }

                $products_options_content_display .='<label class="ModuleProductsInfoOptionsName">'. $QproductsOptionsName->value('products_options_name') . ': </label>';

                foreach ($products_options_array as $value) {
                  $products_options_content_display .='<div class="col-md-12">';
                  $products_options_content_display .='<span class="ModuleProductsInfoOptionsPullDownMenu">' . HTML::radioField('id[' . $QproductsOptionsName->valueInt('products_options_id') . ']', $value['id'], $selected_attribute, 'checked id="defaultCheck' . $value['id'] .'"') . ' ' . $value['text'] . ' ' .'</span>';
                  $products_options_content_display .='</div>';
                }
              }

              $products_options_content_display .='<div class="separator"></div>';
            }// end while

            $products_options_content_display .='</div>';
            $products_options_content_display .='</div>' . "\n";

// Strong relations with pi_products_info_price.php Don't delete
            if(MODULE_PRODUCTS_INFO_PRICE_SORT_ORDER == '') {
               $module_produts_info_price_sort_order = -1;
            } else {
              $module_produts_info_price_sort_order = MODULE_PRODUCTS_INFO_PRICE_SORT_ORDER;
            }

            if ( MODULE_PRODUCTS_INFO_OPTIONS_SORT_ORDER > $module_produts_info_price_sort_order) {
              $products_options_content_display .='</form>' . "\n";
            }

            $products_options_content_display .= '<!-- end products_options -->' . "\n";
            $CLICSHOPPING_Template->addBlock($products_options_content_display, $this->group);

          } // end total
        }

    } // public function execute

    public function isEnabled() {
      return $this->enabled;
    }

    public function check() {
      return defined('MODULE_PRODUCTS_INFO_OPTIONS_STATUS');
    }

    public function install() {
      $CLICSHOPPING_Db = Registry::get('Db');

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Do you want activate this module ?',
          'configuration_key' => 'MODULE_PRODUCTS_INFO_OPTIONS_STATUS',
          'configuration_value' => 'True',
          'configuration_description' => 'Do you want activate this module in your shop ?',
          'configuration_group_id' => '6',
          'sort_order' => '1',
          'set_function' => 'clic_cfg_set_boolean_value(array(\'True\', \'False\'))',
          'date_added' => 'now()'
        ]
      );


      $CLICSHOPPING_Db->save('configuration', [
        'configuration_title' => 'Please select the width of the module',
        'configuration_key' => 'MODULE_PRODUCTS_INFO_OPTIONS_CONTENT_WIDTH',
        'configuration_value' => '12',
        'configuration_description' => 'Select a number between 1 and 12',
        'configuration_group_id' => '6',
        'sort_order' => '1',
        'set_function' => 'clic_cfg_set_content_module_width_pull_down',
        'date_added' => 'now()'
      ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Where do you want display the module ?',
          'configuration_key' => 'MODULE_PRODUCTS_INFO_OPTIONS_POSITION',
          'configuration_value' => 'float-md-none',
          'configuration_description' => 'Affiche l\'option du produit à gauche ou à droite<br><br><i>(Valeur Left = Gauche <br>Valeur Right = Droite <br>Valeur None = Aucun)</i>',
          'configuration_group_id' => '6',
          'sort_order' => '2',
          'set_function' => 'clic_cfg_set_boolean_value(array(\'float-md-right\', \'float-md-left\', \'float-md-none\'),',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Sort order',
          'configuration_key' => 'MODULE_PRODUCTS_INFO_OPTIONS_SORT_ORDER',
          'configuration_value' => '90',
          'configuration_description' => 'Sort order of display. Lowest is displayed first',
          'configuration_group_id' => '6',
          'sort_order' => '3',
          'set_function' => '',
          'date_added' => 'now()'
        ]
      );

      return $CLICSHOPPING_Db->save('configuration', ['configuration_value' => '1'],
                                              ['configuration_key' => 'WEBSITE_MODULE_INSTALLED']
                            );
    }

    public function remove() {
      return Registry::get('Db')->exec('delete from :table_configuration where configuration_key in ("' . implode('", "', $this->keys()) . '")');
    }

    public function keys() {
      return array (
        'MODULE_PRODUCTS_INFO_OPTIONS_STATUS',
        'MODULE_PRODUCTS_INFO_OPTIONS_CONTENT_WIDTH',
        'MODULE_PRODUCTS_INFO_OPTIONS_POSITION',
        'MODULE_PRODUCTS_INFO_OPTIONS_SORT_ORDER'
      );
    }
  }
