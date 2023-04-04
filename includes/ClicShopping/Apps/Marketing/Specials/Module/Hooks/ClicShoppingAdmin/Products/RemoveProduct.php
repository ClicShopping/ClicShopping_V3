<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\Apps\Marketing\Specials\Module\Hooks\ClicShoppingAdmin\Products;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;

  use ClicShopping\Apps\Marketing\Specials\Specials as SpecialsApp;

  class RemoveProduct implements \ClicShopping\OM\Modules\HooksInterface
  {
    protected mixed $app;

    public function __construct()
    {
      if (!Registry::exists('Specials')) {
        Registry::set('Specials', new SpecialsApp());
      }

      $this->app = Registry::get('Specials');
    }

    private function removeProducts($id)
    {
      if (!empty($_POST['products_specials'])) {
        $this->app->db->delete('specials', ['products_id' => (int)$id]);
      }
    }


    public function execute()
    {
      if (isset($_POST['remove_id'])) {
        $pID = HTML::sanitize($_POST['remove_id']);
      } elseif (isset($_POST['pID'])) {
        $pID = HTML::sanitize($_POST['pID']);
      } else {
        $pID = false;
      }

      if ($pID !== false) {
        $this->removeProducts($pID);
      }
    }
  }