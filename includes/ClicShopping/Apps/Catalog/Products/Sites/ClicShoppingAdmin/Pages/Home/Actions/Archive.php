<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\Apps\Catalog\Products\Sites\ClicShoppingAdmin\Pages\Home\Actions;

  use ClicShopping\OM\Registry;

  class Archive extends \ClicShopping\OM\PagesActionsAbstract
  {
    public function execute()
    {
      $CLICSHOPPING_Products = Registry::get('Products');

      $this->page->setFile('archive.php');
      $this->page->data['action'] = 'ArchiveConfirm';

      $CLICSHOPPING_Products->loadDefinitions('Sites/ClicShoppingAdmin/Products');
    }
  }