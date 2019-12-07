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

  namespace ClicShopping\Apps\Configuration\Zones\Sites\ClicShoppingAdmin\Pages\Home\Actions\Zones;

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\Registry;

  class Update extends \ClicShopping\OM\PagesActionsAbstract
  {
    protected $app;

    public function __construct()
    {
      $this->app = Registry::get('Zones');
    }

    public function execute()
    {
      $page = (isset($_GET['page']) && is_numeric($_GET['page'])) ? HTML::sanitize($_GET['page']) : 1;
      $zone_id = HTML::sanitize($_GET['cID']);
      $zone_country_id = HTML::sanitize($_POST['zone_country_id']);
      $zone_code = HTML::sanitize($_POST['zone_code']);
      $zone_name = HTML::sanitize($_POST['zone_name']);

      $this->app->db->save('zones', [
        'zone_country_id' => (int)$zone_country_id,
        'zone_code' => $zone_code,
        'zone_name' => $zone_name
      ], [
          'zone_id' => (int)$zone_id
        ]
      );

      $this->app->redirect('Zones&page=' . $page . '&cID=' . $zone_id);
    }
  }