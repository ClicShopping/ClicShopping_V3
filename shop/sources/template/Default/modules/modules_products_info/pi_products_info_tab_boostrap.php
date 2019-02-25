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

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\CLICSHOPPING;

  class pi_products_info_tab_boostrap {
    public $code;
    public $group;
    public $title;
    public $description;
    public $sort_order;
    public $enabled = false;

    public function __construct() {

      $this->code = get_class($this);
      $this->group = basename(__DIR__);

      $this->title = CLICSHOPPING::getDef('module_products_info_tab_boostap_title');
      $this->description = CLICSHOPPING::getDef('module_products_info_tab_boostap_description');

      if (defined('MODULE_PRODUCTS_INFO_DESCRIPTION_TAB_BOOSTRAP_STATUS')) {
        $this->sort_order = MODULE_PRODUCTS_INFO_DESCRIPTION_TAB_BOOSTRAP_SORT_ORDER;
        $this->enabled = (MODULE_PRODUCTS_INFO_DESCRIPTION_TAB_BOOSTRAP_STATUS == 'True');
      }
    }

    public function execute() {
      if (isset($_GET['products_id']) && isset($_GET['Products']) ) {
        $content_width = (int)MODULE_PRODUCTS_INFO_DESCRIPTION_TAB_BOOSTRAP_CONTENT_WIDTH;

        $CLICSHOPPING_ProductsCommon = Registry::get('ProductsCommon');
        $CLICSHOPPING_Template = Registry::get('Template');

        $footer_tag = '<script>$(document).ready(function(){$(".tab-pane").first().addClass("active");});</script>';
        $CLICSHOPPING_Template->addBlock($footer_tag, 'footer_scripts');


        $desc = $CLICSHOPPING_ProductsCommon->getProductsDescription();

         $product_tab_title = '<div id="descriptionTabs" style="overflow: auto;" class="productsInfoTabBoostrapDescription">';
         $product_tab_title .='<ul class="nav nav-tabs flex-column flex-sm-row" role="tablist" id="myTab">';

          if (strpos($desc, '<tabcatalog>') !== FALSE) {
            $cut = explode('<tabcatalog>', trim($desc));
            $c = 0;

            foreach ($cut as $k => $part) {
              if (trim($part) != '') {
                if (strpos($part, '</tabcatalog>') !== FALSE) {
                  $t = substr($part, 0, strpos($part, '</tabcatalog>'));
                  if ($k = 0) {
                    $class = 'nav-link active';
                  } else {
                    $class = 'nav-link';
                  }

                  $product_tab_title .= '<li class="nav-item"><a href="#tab' . $c . '" role="tab" data-toggle="tab" class="' . $class . '">' . $t . '</a></li>';
                }
              }

              $c++;
            }
          }

          $product_tab_title .= '</ul>';
          $product_tab_title .= '</div>';

          $product_tab_description = '<div class="tabsProductsInfoTabBoostrapDescription">';
          $product_tab_description .= '<div class="tab-content">';

          if (strpos($desc, '<tabcatalog>') !== FALSE) {
            $cut = explode('<tabcatalog>', trim($desc));

            foreach ($cut as $n => $part) {
              if (trim($part) != '') {
                if (strpos($part, '</tabcatalog>') !== FALSE) {
                  $r = substr($part, strpos($part, '</tabcatalog>') + 13);
                  $product_tab_description .= '<div class="tab-pane" id="tab' . $n . '">' . $r . '</div>';
                }
              }
            }
          }

          $products_description_content = '<!-- Start products_description_tab -->' . "\n";

          ob_start();
          require($CLICSHOPPING_Template->getTemplateModules($this->group . '/content/products_info_tab_boostrap'));

          $products_description_content .= ob_get_clean();

          $products_description_content .= '<div>';
          $products_description_content .= '<!-- end products_description_tab -->' . "\n";

          $CLICSHOPPING_Template->addBlock($products_description_content, $this->group);

      } // $_GET['products_id']
    } // public function execute

    public function isEnabled() {
      return $this->enabled;
    }

    public function check() {
      return defined('MODULE_PRODUCTS_INFO_DESCRIPTION_TAB_BOOSTRAP_STATUS');
    }

    public function install() {
      $CLICSHOPPING_Db = Registry::get('Db');

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Do you want activate this module ?',
          'configuration_key' => 'MODULE_PRODUCTS_INFO_DESCRIPTION_TAB_BOOSTRAP_STATUS',
          'configuration_value' => 'True',
          'configuration_description' => 'Note : Use this syntax to create your tab description <tabcatalog></tabcatalog>',
          'configuration_group_id' => '6',
          'sort_order' => '1',
          'set_function' => 'clic_cfg_set_boolean_value(array(\'True\', \'False\'))',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Veuillez selectionner la largeur de l\'affichage?',
          'configuration_key' => 'MODULE_PRODUCTS_INFO_DESCRIPTION_TAB_BOOSTRAP_CONTENT_WIDTH',
          'configuration_value' => '12',
          'configuration_description' => 'Veuillez indiquer un nombre compris entre 1 et 12',
          'configuration_group_id' => '6',
          'sort_order' => '1',
          'set_function' => 'clic_cfg_set_content_module_width_pull_down',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Ordre de tri d\'affichage',
          'configuration_key' => 'MODULE_PRODUCTS_INFO_DESCRIPTION_TAB_BOOSTRAP_SORT_ORDER',
          'configuration_value' => '100',
          'configuration_description' => 'Ordre de tri pour l\'affichage (Le plus petit nombre est montré en premier)',
          'configuration_group_id' => '6',
          'sort_order' => '3',
          'set_function' => '',
          'date_added' => 'now()'
        ]
      );
    }

    public function remove() {
      return Registry::get('Db')->exec('delete from :table_configuration where configuration_key in ("' . implode('", "', $this->keys()) . '")');
    }

    public function keys() {
      return array (
        'MODULE_PRODUCTS_INFO_DESCRIPTION_TAB_BOOSTRAP_STATUS',
        'MODULE_PRODUCTS_INFO_DESCRIPTION_TAB_BOOSTRAP_CONTENT_WIDTH',
        'MODULE_PRODUCTS_INFO_DESCRIPTION_TAB_BOOSTRAP_SORT_ORDER'
      );
    }
  }
