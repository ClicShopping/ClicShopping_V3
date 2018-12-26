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


  namespace ClicShopping\Apps\Configuration\Countries\Sites\ClicShoppingAdmin\Pages\Home\Actions\Countries;

  use ClicShopping\OM\Registry;

  class UpdateAll extends \ClicShopping\OM\PagesActionsAbstract {
    protected $app;

    public function __construct() {
      $this->app = Registry::get('Countries');
    }

    public function execute() {

      if (!empty($_POST['selected'])) {
        foreach ($_POST['selected'] as $id) {

          $Qupdate = $this->app->db->prepare('update :table_countries
                                               set status = 0
                                               where countries_id = :countries_id
                                              ');
          $Qupdate->bindInt(':countries_id', $id);
          $Qupdate->execute();
        }
      }

      $this->app->redirect('Countries&page=' . $_GET['page']);
    }
  }