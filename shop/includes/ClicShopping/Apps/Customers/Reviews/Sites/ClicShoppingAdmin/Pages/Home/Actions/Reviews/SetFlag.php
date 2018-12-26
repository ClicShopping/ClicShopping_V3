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


  namespace ClicShopping\Apps\Customers\Reviews\Sites\ClicShoppingAdmin\Pages\Home\Actions\Reviews;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;

  use ClicShopping\Apps\Customers\Reviews\Classes\ClicShoppingAdmin\Status;

  class SetFlag extends \ClicShopping\OM\PagesActionsAbstract {

    public function execute() {

      $CLICSHOPPING_Reviews = Registry::get('Reviews');

      if (isset($_GET['id'])) {
        Status::getReviewsStatus(HTML::sanitize($_GET['id']), HTML::sanitize($_GET['flag']));
      }

      $CLICSHOPPING_Reviews->redirect('Reviews&Reviews' . (isset($_GET['page']) ? 'page=' . $_GET['page'] . '&' : '') . 'rID=' . $_GET['id']);
    }
  }