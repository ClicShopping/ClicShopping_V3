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

  use ClicShopping\Apps\Configuration\Countries\Classes\ClicShoppingAdmin\Status;


  class SetFlag extends \ClicShopping\OM\PagesActionsAbstract {
    protected $app;

    public function __construct() {
      $this->app = Registry::get('Countries');
    }

    public function execute() {

      Status::getCountriesStatus($_GET['cID'], $_GET['flag']);

      $this->app->redirect('Countries&' . (isset($_GET['page']) ? 'page=' . $_GET['page'] . '&' : ''). 'cID=' . $_GET['id']);
    }
  }