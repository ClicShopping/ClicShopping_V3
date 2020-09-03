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

  namespace ClicShopping\Apps\Catalog\Categories\Sites\ClicShoppingAdmin\Pages\Home\Actions;

  use ClicShopping\OM\Registry;

  class CategoriesPopUp extends \ClicShopping\OM\PagesActionsAbstract
  {

    public function execute()
    {
      $CLICSHOPPING_Categories = Registry::get('Categories');

      $this->page->setUseSiteTemplate(false); //don't display Header / Footer
      $this->page->setFile('categories_popup.php');
      $this->page->data['action'] = 'CategoriesPopUp';

      $CLICSHOPPING_Categories->loadDefinitions('Sites/ClicShoppingAdmin/categories_popup');
    }
  }