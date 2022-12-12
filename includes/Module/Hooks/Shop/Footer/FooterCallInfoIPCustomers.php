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

  namespace ClicShopping\OM\Module\Hooks\Shop\Footer;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\Cache;

  use ClicShopping\Custom\Sites\Common\InfoIPCustomers;

  class FooterCallInfoIPCustomers
  {
    /**
     * @var bool|null
     */
    protected $InfoIPCustomers;

    public function __construct()
    {
      Registry::set('InfoIPCustomers', new InfoIPCustomers());
      $this->InfoIPCustomers = Registry::get('InfoIPCustomers');
      $this->InfoIPCustomers->blockSpider();
    }

/*
 * In stall db if does'nt exist
 * @param
 *
 *
*/
    private function install()
    {
      $CLICSHOPPING_Db = Registry::get('Db');

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Do you want to track the customer inside the navigation ?',
          'configuration_key' => 'CONFIGURATION_CURRENCIES_GEOLOCALISATION',
          'configuration_value' => 'false',
          'configuration_description' => 'Track the customer and take information about the product it look',
          'configuration_group_id' => '1',
          'sort_order' => '9',
          'set_function' => 'clic_cfg_set_boolean_value(array(\'true\', \'false\'))',
          'date_added' => 'now()'
        ]
      );

      Cache::clear('configuration');
    }

      public function execute()
    {

/*
      if (!\defined(CONFIGURATION_CURRENCIES_GEOLOCALISATION)) {
        $this->install();

        Cache::clear('menu-administrator');

      }
*/

       //   $this->InfoIPCustomers->saveData();
    }
  }