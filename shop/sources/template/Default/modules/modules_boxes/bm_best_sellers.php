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
  use ClicShopping\OM\CLICSHOPPING;

  class bm_best_sellers {
    public $code;
    public $group;
    public $title;
    public $description;
    public $sort_order;
    public $enabled = false;
    public $pages;

    public function  __construct() {
      $this->code = get_class($this);
      $this->group = basename(__DIR__);

      $this->title = CLICSHOPPING::getDef('module_boxes_best_sellers_title');
      $this->description = CLICSHOPPING::getDef('module_boxes_best_sellers_description');

      if ( defined('MODULE_BOXES_BEST_SELLERS_STATUS') ) {
        $this->sort_order = MODULE_BOXES_BEST_SELLERS_SORT_ORDER;
        $this->enabled = (MODULE_BOXES_BEST_SELLERS_STATUS == 'True');
        $this->pages = MODULE_BOXES_BEST_SELLERS_DISPLAY_PAGES;
        $this->group = ((MODULE_BOXES_BEST_SELLERS_CONTENT_PLACEMENT == 'Left Column') ? 'boxes_column_left' : 'boxes_column_right');
      }
    }

    public function  execute() {

      $CLICSHOPPING_Customer = Registry::get('Customer');
      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_Template = Registry::get('Template');
      $CLICSHOPPING_Category = Registry::get('Category');
      $CLICSHOPPING_Language = Registry::get('Language');
      $CLICSHOPPING_Service = Registry::get('Service');
      $CLICSHOPPING_Banner = Registry::get('Banner');

      if (!isset($_GET['products_id'])) {
        if ($CLICSHOPPING_Category->getID() && ($CLICSHOPPING_Category->getID() > 0)) {

          if ($CLICSHOPPING_Customer->getCustomersGroupID() != 0) {

            $QBestSellers = $CLICSHOPPING_Db->prepare('select distinct p.products_id,
                                                                pd.products_name
                                                from :table_products p left join :table_products_groups g on p.products_id = g.products_id,
                                                     :table_products_description pd,
                                                     :table_products_to_categories p2c,
                                                     :table_categories c
                                                where p.products_status = 1
                                                and g.customers_group_id = :customers_group_id
                                                and g.products_group_view = 1
                                                and p.products_ordered > 0
                                                and p.products_id = pd.products_id
                                                and pd.language_id = :language_id
                                                and p2c.categories_id = c.categories_id
                                                and :categories_id in (c.categories_id, c.parent_id)
                                                order by p.products_ordered desc,
                                                         pd.products_name
                                                limit :limit
                                           ');
            $QBestSellers->bindInt(':language_id', (int)$CLICSHOPPING_Language->getId());
            $QBestSellers->bindInt(':customers_group_id', $CLICSHOPPING_Customer->getCustomersGroupID());
            $QBestSellers->bindInt(':categories_id', $CLICSHOPPING_Category->getID());
            $QBestSellers->bindInt(':limit', (int)MODULE_BOXES_BEST_SELLERS_MAX_DISPLAY);

            $QBestSellers->execute();

          } else {


            $QBestSellers = $CLICSHOPPING_Db->prepare('select distinct p.products_id,
                                                                pd.products_name
                                                from :table_products p,
                                                     :table_products_description pd,
                                                     :table_products_to_categories p2c,
                                                     :table_categories c
                                                where p.products_status = 1
                                                and p.products_view = 1
                                                and p.products_ordered > 0
                                                and p.products_id = pd.products_id
                                                and pd.language_id = :language_id
                                                and p2c.categories_id = c.categories_id
                                                and :categories_id in (c.categories_id, c.parent_id)
                                                order by p.products_ordered desc,
                                                         pd.products_name
                                                limit :limit
                                           ');
            $QBestSellers->bindInt(':language_id', (int)$CLICSHOPPING_Language->getId());
            $QBestSellers->bindInt(':categories_id', $CLICSHOPPING_Category->getID());
            $QBestSellers->bindInt(':limit', (int)MODULE_BOXES_BEST_SELLERS_MAX_DISPLAY);

            $QBestSellers->execute();

          }
        } else {

          if ($CLICSHOPPING_Customer->getCustomersGroupID() != 0) {

            $QBestSellers = $CLICSHOPPING_Db->prepare('select distinct p.products_id,
                                                                pd.products_name
                                                from :table_products p  left join :table_products_groups g on p.products_id = g.products_id,
                                                     :table_products_description pd
                                                where p.products_status = 1
                                                and g.products_group_view = 1
                                                and g.customers_group_id = :customers_group_id
                                                and p.products_ordered > 0
                                                and p.products_id = pd.products_id
                                                and pd.language_id = :language_id
                                                order by p.products_ordered desc,
                                                         pd.products_name
                                                limit :limit
                                           ');
            $QBestSellers->bindInt(':language_id', (int)$CLICSHOPPING_Language->getId());
            $QBestSellers->bindInt(':customers_group_id', (int)$CLICSHOPPING_Customer->getCustomersGroupID());
            $QBestSellers->bindInt(':limit', (int)MODULE_BOXES_BEST_SELLERS_MAX_DISPLAY);

            $QBestSellers->execute();

          } else {

            $QBestSellers = $CLICSHOPPING_Db->prepare('select distinct p.products_id,
                                                                pd.products_name
                                                from :table_products p,
                                                     :table_products_description pd
                                                where p.products_status = 1
                                                and p.products_view = 1
                                                and p.products_ordered > 0
                                                and p.products_id = pd.products_id
                                                and pd.language_id = :language_id
                                                order by p.products_ordered desc,
                                                         pd.products_name
                                                limit :limit
                                           ');
            $QBestSellers->bindInt(':language_id', (int)$CLICSHOPPING_Language->getId());
            $QBestSellers->bindInt(':limit', (int)MODULE_BOXES_BEST_SELLERS_MAX_DISPLAY);

            $QBestSellers->execute();

          }
        }

        $best_sellers = $QBestSellers->fetchAll();

        if (count($best_sellers) >= MODULE_BOXES_BEST_SELLERS_MIN_DISPLAY && count($best_sellers) <= MODULE_BOXES_BEST_SELLERS_MAX_DISPLAY) {
          $position = 1;

          $bestsellers_list = '<ol class="olBestSellers">';

          foreach ($best_sellers as $b) {
            $bestsellers_list .= '<li class="BestSellerLi">' . HTML::link(CLICSHOPPING::link(null, 'Products&Description&products_id=' . $b['products_id']), $position . '. <span itemprop="itemListElement">' . $b['products_name'] .'</span>') .'</li>';

            $position++;
          }

          $bestsellers_list .= '</ol>';

          if ($CLICSHOPPING_Service->isStarted('Banner') ) {
            if ($banner = $CLICSHOPPING_Banner->bannerExists('dynamic',  MODULE_BOXES_BEST_SELLERS_BANNER_GROUP)) {
              $best_sellers_banner = $CLICSHOPPING_Banner->displayBanner('static', $banner) . '<br /><br />';
            }
          }

          $data ='<!-- Boxe best sellers start -->' . "\n";

          ob_start();
          require($CLICSHOPPING_Template->getTemplateModules('/modules_boxes/content/best_sellers'));

          $data .= ob_get_clean();

          $data .='<!-- Boxe best sellers end -->' . "\n";


          $CLICSHOPPING_Template->addBlock($data, $this->group);
        }
      }
    }

    public function  isEnabled() {
      return $this->enabled;
    }

    public function  check() {
      return defined('MODULE_BOXES_BEST_SELLERS_STATUS');
    }

    public function  install() {
      $CLICSHOPPING_Db = Registry::get('Db');

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Do you want activate this module ?',
          'configuration_key' => 'MODULE_BOXES_BEST_SELLERS_STATUS',
          'configuration_value' => 'True',
          'configuration_description' => 'Do you want activate this module in your shop ?',
          'configuration_group_id' => '6',
          'sort_order' => '1',
          'set_function' => 'clic_cfg_set_boolean_value(array(\'True\', \'False\'))',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Please choose where the boxe must be displayed',
          'configuration_key' => 'MODULE_BOXES_BEST_SELLERS_CONTENT_PLACEMENT',
          'configuration_value' => 'Right Column',
          'configuration_description' => 'Choose where the boxe must be displayed',
          'configuration_group_id' => '6',
          'sort_order' => '2',
          'set_function' => 'clic_cfg_set_boolean_value(array(\'Left Column\', \'Right Column\'),',
          'date_added' => 'now()'
        ]
      );


      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Please indicate the banner group for the image',
          'configuration_key' => 'MODULE_BOXES_BEST_SELLERS_BANNER_GROUP',
          'configuration_value' => SITE_THEMA.'_boxe__bestsellers',
          'configuration_description' => 'Indicate the banner group<br /><br /><strong>Note :</strong><br /><i>The group must be created or selected whtn you create a banner in Marketing / banner</i>',
          'configuration_group_id' => '6',
          'sort_order' => '3',
          'set_function' => '',
          'date_added' => 'now()'
        ]
      );


      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Please indicate a minimum number to display the best sellers',
          'configuration_key' => 'MODULE_BOXES_BEST_SELLERS_MIN_DISPLAY',
          'configuration_value' => '3',
          'configuration_description' => 'Indicate a minimum number to display the best sellers',
          'configuration_group_id' => '6',
          'sort_order' => '4',
          'set_function' => '',
          'date_added' => 'now()'
        ]
      );


      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Please indicate a maximal number to display the best sellers',
          'configuration_key' => 'MODULE_BOXES_BEST_SELLERS_MAX_DISPLAY',
          'configuration_value' => '10',
          'configuration_description' => 'Indicate a maximal number to display the best sellers',
          'configuration_group_id' => '6',
          'sort_order' => '5',
          'set_function' => '',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Sort order',
          'configuration_key' => 'MODULE_BOXES_BEST_SELLERS_SORT_ORDER',
          'configuration_value' => '120',
          'configuration_description' => 'Sort order of display. Lowest is displayed first',
          'configuration_group_id' => '6',
          'sort_order' => '6',
          'set_function' => '',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Indicate the page where the module is displayed',
          'configuration_key' => 'MODULE_BOXES_BEST_SELLERS_DISPLAY_PAGES',
          'configuration_value' => 'all',
          'configuration_description' => 'Sélectionnez les pages où la boxe doit être présente.',
          'configuration_group_id' => '6',
          'sort_order' => '7',
          'set_function' => 'clic_cfg_set_select_pages_list',
          'date_added' => 'now()'
        ]
      );

      return $CLICSHOPPING_Db->save('configuration', ['configuration_value' => '1'],
                                               ['configuration_key' => 'WEBSITE_MODULE_INSTALLED']
                              );

    }

    public function  remove() {
      return Registry::get('Db')->exec('delete from :table_configuration where configuration_key in ("' . implode('", "', $this->keys()) . '")');

    }

    public function  keys() {
      return array('MODULE_BOXES_BEST_SELLERS_STATUS',
                   'MODULE_BOXES_BEST_SELLERS_CONTENT_PLACEMENT',
                   'MODULE_BOXES_BEST_SELLERS_MIN_DISPLAY',
                   'MODULE_BOXES_BEST_SELLERS_MAX_DISPLAY',
                   'MODULE_BOXES_BEST_SELLERS_SORT_ORDER',
                   'MODULE_BOXES_BEST_SELLERS_BANNER_GROUP',
                   'MODULE_BOXES_BEST_SELLERS_DISPLAY_PAGES'
                  );
    }
  }
