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
  use ClicShopping\OM\HTML;
  use ClicShopping\OM\DateTime;
  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\Apps\Customers\Reviews\Classes\Shop\Reviews;
  class pr_products_reviews_info_content {
    public $code;
    public $group;
    public $title;
    public $description;
    public $sort_order;
    public $enabled = false;

    public function __construct() {
      $this->code = get_class($this);
      $this->group = basename(__DIR__);

      $this->title = CLICSHOPPING::getDef('modules_products_reviews_info_content_title');
      $this->description = CLICSHOPPING::getDef('modules_products_reviews_info_content_description');

      if ( defined('MODULES_PRODUCTS_REVIEWS_INFO_CONTENT_STATUS') ) {
        $this->sort_order = MODULES_PRODUCTS_REVIEWS_INFO_CONTENT_SORT_ORDER;
        $this->enabled = (MODULES_PRODUCTS_REVIEWS_INFO_CONTENT_STATUS == 'True');
      }
    }

    public function execute() {

      $CLICSHOPPING_Template = Registry::get('Template');
      $CLICSHOPPING_Reviews = Registry::get('Reviews');
      $CLICSHOPPING_Customer = Registry::get('Customer');

      $content_width = (int)MODULES_PRODUCTS_REVIEWS_INFO_CONTENT_CONTENT_WIDTH;

      if (isset($_GET['Products']) && isset($_GET['ReviewsInfo']) && isset($_GET['reviews_id'])) {

        $CLICSHOPPING_ProductsCommon = Registry::get('ProductsCommon');
        $CLICSHOPPING_ProductsFunctionTemplate = Registry::get('ProductsFunctionTemplate');

        $reviews_id = (int)$_GET['reviews_id'];
        $reviews = $CLICSHOPPING_Reviews->getDataReviews($reviews_id);

        $reviews_text = $reviews['reviews_text'];
        $reviews_rating  = $reviews['reviews_rating'];

        $products_name_url = $CLICSHOPPING_ProductsFunctionTemplate->getProductsUrlRewrited()->getProductNameUrl($CLICSHOPPING_ProductsCommon->getID());

        $products_name = HTML::link($products_name_url, $CLICSHOPPING_ProductsCommon->getProductsName());

        $delete_reviews = '';

        if ($reviews !== false) {
          if ($reviews['customers_id'] == $CLICSHOPPING_Customer->getID()) {
            $delete_reviews .= HTML::form('reviews', CLICSHOPPING::link(null, 'Products&ReviewsInfo&Delete&products_id=' . $CLICSHOPPING_ProductsCommon->getID() . '&reviews_id=' . $reviews_id), 'post', 'id="Reviews"', ['tokenize' => true, 'action' => 'process']);
            $delete_reviews .= HTML::button(null, 'fas fa-trash', null, 'danger', null, 'md');
            $delete_reviews .= '</form>';
          }

          $customer_name  = '*** ' . HTML::outputProtected(substr($reviews['customers_name'], 4, -4)) . ' ***';
          $date_added  = DateTime::toLong($reviews['date_added']);
          $customer_text = HTML::breakString(nl2br(HTML::outputProtected($reviews_text)), 60, '-<br />');
          $customer_rating = '<span class="productsInfoReviewsContentRating" itemprop="ratingValue">' . HTML::stars($reviews_rating) . '</span>';

          $data = '<!-- pr_products_reviews_info_content start -->' . "\n";

          ob_start();
          require_once($CLICSHOPPING_Template->getTemplateModules($this->group . '/content/products_reviews_info_content'));

          $data .= ob_get_clean();

          $data .= '<!-- pr_products_reviews_info_content end -->' . "\n";
        } else {
          $data = '<div class="alert alert-info" role="alert">' .  CLICSHOPPING::getDef('modules_products_reviews_info_content_text_no_review') . '</div>';
        }

        $CLICSHOPPING_Template->addBlock($data, $this->group);
      }
    } // public function execute

    public function isEnabled() {
      return $this->enabled;
    }

    public function check() {
      return defined('MODULES_PRODUCTS_REVIEWS_INFO_CONTENT_STATUS');
    }

    public function install() {
      $CLICSHOPPING_Db = Registry::get('Db');

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Do you want activate this module ?',
          'configuration_key' => 'MODULES_PRODUCTS_REVIEWS_INFO_CONTENT_STATUS',
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
          'configuration_key' => 'MODULES_PRODUCTS_REVIEWS_INFO_CONTENT_CONTENT_WIDTH',
          'configuration_value' => '12',
          'configuration_description' => 'Select a number between 1 and 12',
          'configuration_group_id' => '6',
          'sort_order' => '1',
          'set_function' => 'clic_cfg_set_content_module_width_pull_down',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Sort order',
          'configuration_key' => 'MODULES_PRODUCTS_REVIEWS_INFO_CONTENT_SORT_ORDER',
          'configuration_value' => '30',
          'configuration_description' => 'Sort order of display. Lowest is displayed first',
          'configuration_group_id' => '6',
          'sort_order' => '4',
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
      return array('MODULES_PRODUCTS_REVIEWS_INFO_CONTENT_STATUS',
                   'MODULES_PRODUCTS_REVIEWS_INFO_CONTENT_CONTENT_WIDTH',
                   'MODULES_PRODUCTS_REVIEWS_INFO_CONTENT_SORT_ORDER'
                  );
    }
  }
