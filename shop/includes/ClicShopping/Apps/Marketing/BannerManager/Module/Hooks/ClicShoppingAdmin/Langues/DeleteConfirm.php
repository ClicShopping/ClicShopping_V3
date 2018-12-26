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

  namespace ClicShopping\Apps\Marketing\BannerManager\Module\Hooks\ClicShoppingAdmin\Langues;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;

  use ClicShopping\Apps\Marketing\BannerManager\BannerManager as BannerManagerApp;

  class DeleteConfirm implements \ClicShopping\OM\Modules\HooksInterface {
    protected $app;

    public function __construct()   {
      if (!Registry::exists('BannerManager')) {
        Registry::set('BannerManager', new BannerManagerApp());
      }

      $this->app = Registry::get('BannerManager');
    }

    private function delete($id) {
      if (!is_null($id)) {
        $this->app->db->delete('banners', ['languages_id' => $id]);
      }
    }

    public function execute() {
      if (isset($_GET['DeleteConfirm'])) {
        $id = HTML::sanitize($_GET['lID']);
        $this->delete($id);
      }
    }
  }