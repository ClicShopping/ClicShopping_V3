<?php
/**
 *
 *  @copyright 2008 - https://www.clicshopping.org
 *  @Brand : ClicShopping(Tm) at Inpi all right Reserved
 *  @Licence GPL 2 & MIT

 *  @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\HTML;

  class pr_products_reviews_write_invisible_antispam {
    public string $code;
    public $group;
    public $title;
    public $description;
    public ?int $sort_order = 0;
    public bool $enabled = false;

    public function __construct()
    {
      $this->code = get_class($this);
      $this->group = basename(__DIR__);

      $this->title = CLICSHOPPING::getDef('modules_products_reviews_write_invisible_antispam_title');
      $this->description = CLICSHOPPING::getDef('modules_products_reviews_write_invisible_antispam_description');

      if (\defined('CLICSHOPPING_APP_ANTISPAM_AM_STATUS')) {
        if (CLICSHOPPING_APP_ANTISPAM_AM_REVIEWS_WRITE == 'True' && CLICSHOPPING_APP_ANTISPAM_AM_REVIEWS_WRITE == 'True') {
          if (\defined('MODULES_PRODUCTS_REVIEWS_WRITE_INVISIBLE_ANTISPAM_STATUS')) {
            $this->enabled = (MODULES_PRODUCTS_REVIEWS_WRITE_INVISIBLE_ANTISPAM_STATUS  == 'True');
            $this->sort_order = (int)MODULES_PRODUCTS_REVIEWS_WRITE_INVISIBLE_ANTISPAM_SORT_ORDER ?? 0;
          }
        } else {
          $this->enabled = false;
        }
      }
    }

    public function execute()
    {
      $CLICSHOPPING_Template = Registry::get('Template');

      if (isset($_GET['Products'], $_GET['ReviewsWrite']) && !isset($_GET['Success'])) {
        $products_reviews_write_invisible_antispam = '<!--  products_reviews_write_invisible_invisible_antispam start -->' . "\n";
        $products_reviews_write_invisible_antispam .= HTML::inputField('invisible_clicshopping', '', 'id="hiddenRecaptcha"', null, null, 'hiddenRecaptcha');
        $products_reviews_write_invisible_antispam .= '<!-- products_reviews_write_invisible_invisible_antispam end -->' . "\n";

        $CLICSHOPPING_Template->addBlock($products_reviews_write_invisible_antispam, $this->group);
      }
    }

    public function isEnabled()
    {
      return $this->enabled;
    }

    public function check()
    {
      return \defined('MODULES_PRODUCTS_REVIEWS_WRITE_INVISIBLE_ANTISPAM_STATUS');
    }

    public function install()
    {
      $CLICSHOPPING_Db = Registry::get('Db');

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Do you want to enable this module ?',
          'configuration_key' => 'MODULES_PRODUCTS_REVIEWS_WRITE_INVISIBLE_ANTISPAM_STATUS',
          'configuration_value' => 'True',
          'configuration_description' => 'Do you want to enable this module in your shop ?',
          'configuration_group_id' => '6',
          'sort_order' => '1',
          'set_function' => 'clic_cfg_set_boolean_value(array(\'True\', \'False\'))',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Sort order',
          'configuration_key' => 'MODULES_PRODUCTS_REVIEWS_WRITE_INVISIBLE_ANTISPAM_SORT_ORDER',
          'configuration_value' => '550',
          'configuration_description' => 'Sort order of display. Lowest is displayed first. The sort order must be different on every module',
          'configuration_group_id' => '6',
          'sort_order' => '10',
          'set_function' => '',
          'date_added' => 'now()'
        ]
      );
    }

    public function remove()
    {
      return Registry::get('Db')->exec('delete from :table_configuration where configuration_key in ("' . implode('", "', $this->keys()) . '")');
    }

    public function keys()
    {
      return ['MODULES_PRODUCTS_REVIEWS_WRITE_INVISIBLE_ANTISPAM_STATUS',
              'MODULES_PRODUCTS_REVIEWS_WRITE_INVISIBLE_ANTISPAM_SORT_ORDER'
             ];
    }
  }
