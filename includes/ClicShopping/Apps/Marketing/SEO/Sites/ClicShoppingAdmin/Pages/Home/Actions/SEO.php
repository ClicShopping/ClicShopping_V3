<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\Apps\Marketing\SEO\Sites\ClicShoppingAdmin\Pages\Home\Actions;

  use ClicShopping\OM\Registry;

  class SEO extends \ClicShopping\OM\PagesActionsAbstract
  {
    public function execute()
    {
      $CLICSHOPPING_SEO = Registry::get('SEO');

      $this->page->setFile('seo.php');
      $this->page->data['action'] = 'SEO';

      $CLICSHOPPING_SEO->loadDefinitions('Sites/ClicShoppingAdmin/SEO');
    }
  }