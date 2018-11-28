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

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\CLICSHOPPING;

  class pi_products_info_gallery {
    public $code;
    public $group;
    public $title;
    public $description;
    public $sort_order;
    public $enabled = false;
    protected $lang;

    public function __construct() {
      $this->code = get_class($this);
      $this->group = basename(__DIR__);

      $this->title = CLICSHOPPING::getDef('module_products_info_gallery_title');
      $this->description = CLICSHOPPING::getDef('module_products_info_gallery_description');

      $this->lang = Registry::get('Language');

      if (defined('MODULE_PRODUCTS_INFO_GALLERY_STATUS')) {
        $this->sort_order = MODULE_PRODUCTS_INFO_GALLERY_SORT_ORDER;
        $this->enabled = (MODULE_PRODUCTS_INFO_GALLERY_STATUS == 'True');
      }
    }

    public function execute() {

      if (isset($_GET['products_id']) && isset($_GET['Products']) ) {
        $content_width = (int)MODULE_PRODUCTS_INFO_GALLERY_CONTENT_WIDTH;
        $text_position = MODULE_PRODUCTS_INFO_GALLERY_POSITION;

        $CLICSHOPPING_ProductsCommon = Registry::get('ProductsCommon');
        $CLICSHOPPING_Template = Registry::get('Template');
        $CLICSHOPPING_Db = Registry::get('Db');

        $Qproducts = $CLICSHOPPING_Db->get('products', ['products_id',  'products_image', 'products_image_zoom', 'products_image_medium'], ['products_id' => $CLICSHOPPING_ProductsCommon->getID(), 'products_status' => 1]);

        $products_small_image = $Qproducts->value('products_image');
        $products_image_zoom = $Qproducts->value('products_image_zoom');
        $products_image_medium = $Qproducts->value('products_image_medium');

        $id = $CLICSHOPPING_ProductsCommon->getID();
        $products_name = $CLICSHOPPING_ProductsCommon->getProductsName($id);

        $head = '<!--magnificPopup start  -->' . "\n";
        $head .= '<link rel="stylesheet" type="text/css" media="all" href="' . $CLICSHOPPING_Template->getTemplateDefaultJavaScript('magnific/magnific-popup.css') . '">' . "\n";
        $head .= '<!--magnificPopup  end  -->' . "\n";

        $CLICSHOPPING_Template->addBlock($head, 'header_tags');

        $footer_tag = '<!-- magnificPopup start  -->' . "\n";
        $footer_tag .= '<script src="' . $CLICSHOPPING_Template->getTemplateDefaultJavaScript('magnific/jquery.magnific-popup.min.js') . '"></script>' . "\n";
        $footer_tag .= '<script src="' . $CLICSHOPPING_Template->getTemplateDefaultJavaScript('magnific/ClicShopping/gallery.js') . '"></script>' . "\n";

        $footer_tag .= '<!--magnificPopup  end  -->' . "\n";

        $CLICSHOPPING_Template->addBlock($footer_tag, 'footer_scripts');

        $products_image = '<!-- Start gallery -->' . "\n";

        if ($CLICSHOPPING_ProductsCommon->getProductsTickerSpecials() == 'True' && MODULE_PRODUCTS_INFO_GALLERY_TICKER == 'True') {
          $ticker_image = HTML::tickerImage(CLICSHOPPING::getDef('text_ticker_specials'), 'ModulesProductsInfoBootstrapTickerSpecialImageGallery', $CLICSHOPPING_ProductsCommon->getProductsTickerSpecials());
        } elseif ($CLICSHOPPING_ProductsCommon->getProductsTickerFavorites() == 'True' && MODULE_PRODUCTS_INFO_GALLERY_TICKER == 'True') {
          $ticker_image = HTML::tickerImage(CLICSHOPPING::getDef('text_ticker_favorite'), 'ModulesProductsInfoBootstrapTickerFavoriteImageGallery', $CLICSHOPPING_ProductsCommon->getProductsTickerFavorites());
        } elseif ($CLICSHOPPING_ProductsCommon->getProductsTickerFeatured() == 'True' && MODULE_PRODUCTS_INFO_GALLERY_TICKER == 'True') {
          $ticker_image = HTML::tickerImage(CLICSHOPPING::getDef('text_ticker_featured'), 'ModulesProductsInfoBootstrapTickerFeaturedImageGallery', $CLICSHOPPING_ProductsCommon->getProductsTickerFeatured());
        } elseif ($CLICSHOPPING_ProductsCommon->getProductsTickerProductsNew() == 'True' && MODULE_PRODUCTS_INFO_GALLERY_TICKER == 'True') {
          $ticker_image = HTML::tickerImage(CLICSHOPPING::getDef('text_ticker_products_new'), 'ModulesProductsInfoBootstrapTickerNewImageGallery', $CLICSHOPPING_ProductsCommon->getProductsTickerProductsNew());
        }

        if (MODULE_PRODUCTS_INFO_GALLERY_POURCENTAGE == 'True' && !is_null($CLICSHOPPING_ProductsCommon->getProductsTickerSpecialsPourcentage($CLICSHOPPING_ProductsCommon->getID()))) {
          $ticker_pourcentage_discount = HTML::tickerImage($CLICSHOPPING_ProductsCommon->getProductsTickerSpecialsPourcentage($CLICSHOPPING_ProductsCommon->getID()), 'ModulesProductsInfoBootstrapTickerPourcentageImageGallery', true);
        } else {
          $ticker_pourcentage_discount = '';
        }

        ob_start();
        require($CLICSHOPPING_Template->getTemplateModules($this->group . '/content/products_info_gallery'));
        $products_image .= ob_get_clean();

        $products_image .= '<!-- end gallery -->' . "\n";

        $CLICSHOPPING_Template->addBlock($products_image, $this->group);
      }
    } // public function execute

    public function isEnabled() {
      return $this->enabled;
    }

    public function check() {
      return defined('MODULE_PRODUCTS_INFO_GALLERY_STATUS');
    }

    public function install() {
      $CLICSHOPPING_Db = Registry::get('Db');

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Do you want display this module ?',
          'configuration_key' => 'MODULE_PRODUCTS_INFO_GALLERY_STATUS',
          'configuration_value' => 'True',
          'configuration_description' => 'Activate the module in your shop ?',
          'configuration_group_id' => '6',
          'sort_order' => '1',
          'set_function' => 'clic_cfg_set_boolean_value(array(\'True\', \'False\'))',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Please, select the content size',
          'configuration_key' => 'MODULE_PRODUCTS_INFO_GALLERY_CONTENT_WIDTH',
          'configuration_value' => '4',
          'configuration_description' => 'Please, specif a number betwen 1 ad 12',
          'configuration_group_id' => '6',
          'sort_order' => '1',
          'set_function' => 'clic_cfg_set_content_module_width_pull_down',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Where do want to display the gallery position ?',
          'configuration_key' => 'MODULE_PRODUCTS_INFO_GALLERY_POSITION',
          'configuration_value' => 'float-md-none',
          'configuration_description' => 'select the good value',
          'configuration_group_id' => '6',
          'sort_order' => '2',
          'set_function' => 'clic_cfg_set_boolean_value(array(\'float-md-right\', \'float-md-left\', \'float-md-none\'))',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Please select the width of the thumbail ?',
          'configuration_key' => 'MODULE_PRODUCTS_INFO_GALLERY_THUMBAIL_WIDTH',
          'configuration_value' => '70',
          'configuration_description' => 'Please write a number in px',
          'configuration_group_id' => '6',
          'sort_order' => '1',
          'set_function' => '',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Please select the height of the thumbail ?',
          'configuration_key' => 'MODULE_PRODUCTS_INFO_GALLERY_THUMBAIL_HEIGHT',
          'configuration_value' => '70',
          'configuration_description' => 'Please write a number in px',
          'configuration_group_id' => '6',
          'sort_order' => '1',
          'set_function' => '',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Do you want to display a message like new / special/ featured, favorite ?',
          'configuration_key' => 'MODULE_PRODUCTS_INFO_GALLERY_TICKER',
          'configuration_value' => 'False',
          'configuration_description' => ' display a message like new / special/ featured, favorite on the product<br /><br />The delay can be set in Configuration / Shop / minimal, maximal values',
          'configuration_group_id' => '6',
          'sort_order' => '9',
          'set_function' => 'clic_cfg_set_boolean_value(array(\'True\', \'False\'))',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Do you want to display price discount in poucentage ?',
          'configuration_key' => 'MODULE_PRODUCTS_INFO_GALLERY_POURCENTAGE',
          'configuration_value' => 'False',
          'configuration_description' => 'Display price discount in poucentage',
          'configuration_group_id' => '6',
          'sort_order' => '1',
          'set_function' => 'clic_cfg_set_boolean_value(array(\'True\', \'False\'))',
          'date_added' => 'now()'
        ]
      );


      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Sort Order',
          'configuration_key' => 'MODULE_PRODUCTS_INFO_GALLERY_SORT_ORDER',
          'configuration_value' => '100',
          'configuration_description' => 'Sort Order(Lowest is displayed in first)',
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
        'MODULE_PRODUCTS_INFO_GALLERY_STATUS',
        'MODULE_PRODUCTS_INFO_GALLERY_CONTENT_WIDTH',
        'MODULE_PRODUCTS_INFO_GALLERY_POSITION',
        'MODULE_PRODUCTS_INFO_GALLERY_THUMBAIL_WIDTH',
        'MODULE_PRODUCTS_INFO_GALLERY_THUMBAIL_HEIGHT',
        'MODULE_PRODUCTS_INFO_GALLERY_TICKER',
        'MODULE_PRODUCTS_INFO_GALLERY_POURCENTAGE',
        'MODULE_PRODUCTS_INFO_GALLERY_SORT_ORDER'
      );
    }

    public function getVideo($video) {
        $item = ['youtube', 'vimeo', 'dailymotion'];

        foreach ($item as $result) {
            if (stripos($video, $result) !== false) {
                return true;
            }
        }
        return false;
    }
  }
