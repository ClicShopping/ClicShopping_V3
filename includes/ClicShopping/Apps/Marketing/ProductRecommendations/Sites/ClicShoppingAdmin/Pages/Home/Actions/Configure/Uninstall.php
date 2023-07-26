<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\Apps\Marketing\ProductRecommendations\Sites\ClicShoppingAdmin\Pages\Home\Actions\Configure;

  use ClicShopping\OM\Registry;

  class Uninstall extends \ClicShopping\OM\PagesActionsAbstract
  {

    public function execute()
    {

      $CLICSHOPPING_MessageStack = Registry::get('MessageStack');
      $CLICSHOPPING_ProductRecommendations = Registry::get('ProductRecommendations');

      $current_module = $this->page->data['current_module'];
      $m = Registry::get('ProductRecommendationsAdminConfig' . $current_module);
      $m->uninstall();

      $CLICSHOPPING_MessageStack->add($CLICSHOPPING_ProductRecommendations->getDef('alert_module_uninstall_success'), 'success', 'ProductRecommendations');

      $CLICSHOPPING_ProductRecommendations->redirect('Configure&module=' . $current_module);
    }
  }