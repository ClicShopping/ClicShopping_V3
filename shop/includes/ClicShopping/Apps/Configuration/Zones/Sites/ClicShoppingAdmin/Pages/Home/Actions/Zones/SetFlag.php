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

  use ClicShopping\OM\Registry;

  use ClicShopping\Apps\Configuration\Zones\Classes\ClicShoppingAdmin\Status;

  class SetFlag extends \ClicShopping\OM\PagesActionsAbstract
  {
    protected $app;

    public function __construct()
    {
      $this->app = Registry::get('Zones');
    }

    public function execute()
    {
      $page = (isset($_GET['page']) && is_numeric($_GET['page'])) ? $_GET['page'] : 1;
      Status::getZonesStatus($_GET['id'], $_GET['flag']);

      $this->app->redirect('Zones&' . $_GET['page'] . 'page=' . $page . '&cID=' . $_GET['id']);
    }
  }