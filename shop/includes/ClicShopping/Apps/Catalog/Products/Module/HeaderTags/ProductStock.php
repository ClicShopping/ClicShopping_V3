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

  namespace ClicShopping\Apps\Catalog\Products\Module\HeaderTags;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTTP;
  use ClicShopping\OM\HTML;
  use ClicShopping\OM\CLICSHOPPING;

  use ClicShopping\Apps\Catalog\Products\Products as ProductsApp;

  class ProductStock extends \ClicShopping\OM\Modules\HeaderTagsAbstract {

    protected $lang;
    protected $app;
    protected $group;

    protected function init() {
      if (!Registry::exists('Products')) {
        Registry::set('Products', new ProductsApp());
      }

      $this->app = Registry::get('Products');
      $this->lang = Registry::get('Language');
      $this->group = 'header_tags'; // could be header_tags or footer_scripts

      $this->app->loadDefinitions('Module/HeaderTags/product_stock');

      $this->title = $this->app->getDef('module_header_tags_product_stock_title');
      $this->description = $this->app->getDef('module_header_tags_product_stock_description');

      if ( defined('MODULE_HEADER_TAGS_PRODUCTS_STOCK_STATUS') ) {
        $this->sort_order = (int)MODULE_HEADER_TAGS_PRODUCTS_STOCK_SORT_ORDER;
        $this->enabled = (MODULE_HEADER_TAGS_PRODUCTS_STOCK_STATUS == 'True');
      }
    }

    public function isEnabled() {
      return $this->enabled;
    }

    public function getOutput() {
      $CLICSHOPPING_Template = Registry::get('Template');
      $CLICSHOPPING_ProductsCommon = Registry::get('ProductsCommon');

      if (!defined('CLICSHOPPING_APP_CATALOG_PRODUCTS_PD_STATUS') || CLICSHOPPING_APP_CATALOG_PRODUCTS_PD_STATUS == 'False') {
        return false;
      }

      if (isset($_GET['Products']) && isset($_GET['Description'])) {
        if ( $CLICSHOPPING_ProductsCommon->getProductsStock() > 0) {
          $stock = 'InStock';
        } else {
          $stock = 'OutofStock';
        }

        if (STOCK_ALLOW_CHECKOUT == 'True') {
          $stock = 'InStock';
        }

        $products_packaging = $CLICSHOPPING_ProductsCommon->getProductsPackaging();
        if ($products_packaging == 0) $products_packaging = 'http://schema.org/NewCondition'; // default newCondition
        if ($products_packaging == 1) $products_packaging = 'http://schema.org/NewCondition';
        if ($products_packaging == 2) $products_packaging = 'http://schema.org/RefurbishedCondition';
        if ($products_packaging == 3) $products_packaging = 'http://schema.org/UsedCondition';

        $sku = $CLICSHOPPING_ProductsCommon->getProductsSKU();
        $price = floatval(preg_replace('/[^\d\.]/', '', $CLICSHOPPING_ProductsCommon->getCustomersPrice()));

        $result = '<!--  products json_ltd -->' . "\n";
        $result .= '
  <script type="application/ld+json">
		{
   "@context" : "http://schema.org",
   "@type": "Product",
   "brand": {
             "@type": "Brand",
             "name": "' . $CLICSHOPPING_ProductsCommon->getProductsName() . '"
             },
   "sku": "' . $sku . '",
   "description": "' . $CLICSHOPPING_ProductsCommon->getProductsDescription() . '",
   "url": "' .  CLICSHOPPING::link(null, 'Products&Description&products_id=' . (int)$CLICSHOPPING_ProductsCommon->getID()) .  '",
   "name": "' . $CLICSHOPPING_ProductsCommon->getProductsName() . '",
   "image": "' . CLICSHOPPING::getConfig('http_server', 'Shop') . '/' . $CLICSHOPPING_Template->getDirectoryTemplateImages() . $CLICSHOPPING_ProductsCommon->getProductsImage() . '",
   "itemCondition": "http://schema.org/' . $products_packaging .'",
   "offers": [
                {
                 "@type": "Offer",
                 "price": "' . $price . '",
                 "priceCurrency": "' . HTML::output($_SESSION['currency']) . '",
                 "itemCondition": "' . $products_packaging . '",
                 "url": "' .  CLICSHOPPING::link(null, 'Products&Description&products_id=' . (int)$CLICSHOPPING_ProductsCommon->getID()) .  '",
                 "sku": "' . $sku . '",
                 "availability": "' . $stock . '"
                }
              ]
   }
   ' . "\n";

        $result .= '</script>' . "\n";

        $result .= '<!--  products json_ltd -->' . "\n";

        $display_result = $CLICSHOPPING_Template->addBlock($result, $this->group);

        $output =
<<<EOD
{$display_result}
EOD;
      }

      return $output;
    }

    public function Install() {
      $this->app->db->save('configuration', [
          'configuration_title' => 'Do you want install this module ?',
          'configuration_key' => 'MODULE_HEADER_TAGS_PRODUCTS_STOCK_STATUS',
          'configuration_value' => 'True',
          'configuration_description' => 'Do you want install this module ?',
          'configuration_group_id' => '6',
          'sort_order' => '1',
          'set_function' => 'clic_cfg_set_boolean_value(array(\'True\', \'False\'))',
          'date_added' => 'now()'
        ]
      );

      $this->app->db->save('configuration', [
          'configuration_title' => 'Display sort order',
          'configuration_key' => 'MODULE_HEADER_TAGS_PRODUCTS_STOCK_SORT_ORDER',
          'configuration_value' => '45',
          'configuration_description' => 'Display sort order (The lower is displayd in first)',
          'configuration_group_id' => '6',
          'sort_order' => '15',
          'set_function' => '',
          'date_added' => 'now()'
        ]
      );
    }

    public function keys() {
      return ['MODULE_HEADER_TAGS_PRODUCTS_STOCK_STATUS',
              'MODULE_HEADER_TAGS_PRODUCTS_STOCK_SORT_ORDER'
             ];
    }
  }
