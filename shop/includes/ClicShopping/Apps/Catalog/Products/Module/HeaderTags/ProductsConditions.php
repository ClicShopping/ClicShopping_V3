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

  namespace ClicShopping\Apps\Catalog\Products\Module\HeaderTags;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTTP;
  use ClicShopping\OM\HTML;
  use ClicShopping\OM\CLICSHOPPING;

  use ClicShopping\Apps\Catalog\Products\Products as ProductsApp;

  class ProductsConditions extends \ClicShopping\OM\Modules\HeaderTagsAbstract
  {

    protected $lang;
    protected $app;
    protected $group;

    protected function init()
    {
      if (!Registry::exists('Products')) {
        Registry::set('Products', new ProductsApp());
      }

      $this->app = Registry::get('Products');
      $this->lang = Registry::get('Language');
      $this->group = 'footer_scripts'; // could be header_tags or footer_scripts

      $this->app->loadDefinitions('Module/HeaderTags/products_conditions');

      $this->title = $this->app->getDef('module_header_tags_product_condition_title');
      $this->description = $this->app->getDef('module_header_tags_product_condition_description');

      if (defined('MODULE_HEADER_TAGS_PRODUCT_CONDITION_STATUS')) {
        $this->sort_order = (int)MODULE_HEADER_TAGS_PRODUCT_CONDITION_SORT_ORDER;
        $this->enabled = (MODULE_HEADER_TAGS_PRODUCT_CONDITION_STATUS == 'True');
      }
    }

    public function isEnabled()
    {
      return $this->enabled;
    }

    public function getOutput()
    {
      $CLICSHOPPING_Template = Registry::get('Template');
      $CLICSHOPPING_ProductsCommon = Registry::get('ProductsCommon');
      $CLICSHOPPING_ProductsFunctionTemplate = Registry::get('ProductsFunctionTemplate');

      if (!defined('CLICSHOPPING_APP_CATALOG_PRODUCTS_PD_STATUS') || CLICSHOPPING_APP_CATALOG_PRODUCTS_PD_STATUS == 'False') {
        return false;
      }

      $products_id = $CLICSHOPPING_ProductsCommon->getId();

      if (isset($_GET['Products']) && isset($_GET['Description'])) {
        $jsonLtd = $CLICSHOPPING_ProductsFunctionTemplate->getProductJsonLd($products_id);

        $footer_tag = '<!-- products condition json_ltd -->' . "\n";
        $footer_tag .= $jsonLtd . "\n";
        $footer_tag .= '<!-- end products condition json_ltd -->' . "\n";

        $display_result = $CLICSHOPPING_Template->addBlock($footer_tag,  $this->group);

        $output =
<<<EOD
{$display_result}
EOD;

        return $output;
      }
    }

    public function Install()
    {
      $this->app->db->save('configuration', [
          'configuration_title' => 'Do you want install this module ?',
          'configuration_key' => 'MODULE_HEADER_TAGS_PRODUCT_CONDITION_STATUS',
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
          'configuration_key' => 'MODULE_HEADER_TAGS_PRODUCT_CONDITION_SORT_ORDER',
          'configuration_value' => '162',
          'configuration_description' => 'Display sort order (The lower is displayd in first)',
          'configuration_group_id' => '6',
          'sort_order' => '215',
          'set_function' => '',
          'date_added' => 'now()'
        ]
      );
    }

    public function keys()
    {
      return ['MODULE_HEADER_TAGS_PRODUCT_CONDITION_STATUS',
        'MODULE_HEADER_TAGS_PRODUCT_CONDITION_SORT_ORDER'
      ];
    }
  }
